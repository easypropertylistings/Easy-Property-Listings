/**
 * EPL Unified Listings - QuickEditModal Component
 *
 * Dynamically renders fields based on config.quickEditFields from PHP.
 *
 * @package EPL
 * @since 4.0.0
 */

import { useState, useEffect, useMemo } from '@wordpress/element';
import {
        Modal,
        Button,
        TextControl,
        SelectControl,
        ComboboxControl,
        ToggleControl,
        Spinner,
        Flex,
        FlexItem,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { useConfig } from '../store';
import { updateListing } from '../utils/api';

/**
 * Get a nested property from an object using dot notation.
 * e.g., getNestedValue(obj, 'author.id') or getNestedValue(obj, 'locationSlugs.0')
 */
function getNestedValue(obj, path) {
        if (!obj || !path) return undefined;
        return path.split('.').reduce((acc, part) => {
                if (acc === undefined || acc === null) return undefined;
                return acc[part];
        }, obj);
}

export default function QuickEditModal({ listing, onClose, onSave }) {
        const config = useConfig();
        const [values, setValues] = useState({});
        const [saving, setSaving] = useState(false);
        const [error, setError] = useState(null);

        // Get field definitions from config
        const fieldDefinitions = config.quickEditFields || [];

        // Resolve options for a field
        const resolveOptions = (field) => {
                const { options, type } = field;

                if (!options) return [];

                // If options is an array, use it directly
                if (Array.isArray(options)) {
                        return options.map((opt) => ({
                                value: String(opt.value),
                                label: opt.label,
                        }));
                }

                // If options is a string, it references a config key
                if (typeof options === 'string') {
                        switch (options) {
                                case 'statusLabels': {
                                        const statusLabels = config.statusLabels || {};
                                        return Object.entries(statusLabels).map(([value, label]) => ({
                                                value,
                                                label,
                                        }));
                                }
                                case 'authors': {
                                        const authors = config.authors || [];
                                        return authors.map((author) => ({
                                                value: String(author.id),
                                                label: author.name,
                                        }));
                                }
                                case 'suburbs': {
                                        const suburbs = config.suburbs || [];
                                        return suburbs.map((suburb) => ({
                                                value: suburb.value,
                                                label: suburb.label,
                                        }));
                                }
                                default:
                                        return [];
                        }
                }

                return [];
        };

        // Initialize values from listing based on field definitions
        useEffect(() => {
                if (listing && fieldDefinitions.length > 0) {
                        const initialValues = {};
                        fieldDefinitions.forEach((field) => {
                                const apiKey = field.apiKey || field.key;
                                let value = getNestedValue(listing, apiKey);

                                // Handle special cases
                                if (field.type === 'toggle') {
                                        value = value === true || value === 'yes' || value === '1';
                                } else if (value === undefined || value === null) {
                                        value = '';
                                } else {
                                        value = String(value);
                                }

                                initialValues[field.key] = value;
                        });
                        setValues(initialValues);
                }
        }, [listing, fieldDefinitions]);

        const handleChange = (key, value) => {
                setValues((prev) => ({ ...prev, [key]: value }));
        };

        const handleSave = async () => {
                setSaving(true);
                setError(null);

                try {
                        // Build updates object from field definitions
                        const updates = {};
                        fieldDefinitions.forEach((field) => {
                                const value = values[field.key];
                                if (value !== undefined) {
                                        if (field.type === 'toggle') {
                                                // Convert boolean to 'yes'/'no' or 'yes'/''
                                                updates[field.key] = value ? 'yes' : (field.key === 'property_featured' ? 'no' : '');
                                        } else {
                                                updates[field.key] = value;
                                        }
                                }
                        });

                        await updateListing(listing.id, updates);

                        if (onSave) {
                                onSave();
                        }
                        onClose();
                } catch (err) {
                        setError(err.message || 'Failed to save changes');
                } finally {
                        setSaving(false);
                }
        };

        // Check if a field should be visible based on showFor/hideFor
        const isFieldVisible = (field) => {
                const postType = listing?.postType;
                if (!postType) return true;

                if (field.showFor && field.showFor.length > 0) {
                        return field.showFor.includes(postType);
                }
                if (field.hideFor && field.hideFor.length > 0) {
                        return !field.hideFor.includes(postType);
                }
                return true;
        };

        // Group fields by their group property
        const groupedFields = useMemo(() => {
                const groups = {};
                const ungrouped = [];

                fieldDefinitions.forEach((field) => {
                        if (!isFieldVisible(field)) return;

                        if (field.group) {
                                if (!groups[field.group]) {
                                        groups[field.group] = [];
                                }
                                groups[field.group].push(field);
                        } else {
                                ungrouped.push(field);
                        }
                });

                return { groups, ungrouped };
        }, [fieldDefinitions, listing]);

        // Render a single field
        const renderField = (field) => {
                const options = resolveOptions(field);

                switch (field.type) {
                        case 'select':
                                return (
                                        <SelectControl
                                                key={field.key}
                                                label={field.label}
                                                value={values[field.key] || ''}
                                                options={[
                                                        { value: '', label: __('Select...', 'easy-property-listings') },
                                                        ...options,
                                                ]}
                                                onChange={(value) => handleChange(field.key, value)}
                                        />
                                );

                        case 'combobox':
                                return (
                                        <ComboboxControl
                                                key={field.key}
                                                label={field.label}
                                                value={values[field.key] || ''}
                                                options={options}
                                                onChange={(value) => handleChange(field.key, value)}
                                                allowReset
                                        />
                                );

                        case 'toggle':
                                return (
                                        <ToggleControl
                                                key={field.key}
                                                label={field.label}
                                                checked={!!values[field.key]}
                                                onChange={(value) => handleChange(field.key, value)}
                                        />
                                );

                        case 'number':
                                return (
                                        <TextControl
                                                key={field.key}
                                                label={field.label}
                                                type="number"
                                                value={values[field.key] || ''}
                                                onChange={(value) => handleChange(field.key, value)}
                                        />
                                );

                        case 'text':
                        default:
                                return (
                                        <TextControl
                                                key={field.key}
                                                label={field.label}
                                                value={values[field.key] || ''}
                                                onChange={(value) => handleChange(field.key, value)}
                                        />
                                );
                }
        };

        // Render a group of fields in a Flex row
        const renderGroup = (groupName, fields) => {
                if (fields.length === 0) return null;

                return (
                        <Flex key={groupName} gap={4}>
                                {fields.map((field) => (
                                        <FlexItem key={field.key} style={{ flex: 1 }}>
                                                {renderField(field)}
                                        </FlexItem>
                                ))}
                        </Flex>
                );
        };

        if (!listing) return null;

        return (
                <Modal
                        title={__('Quick Edit', 'easy-property-listings')}
                        onRequestClose={onClose}
                        className="epl-quick-edit-modal"
                >
                        <div className="epl-quick-edit-content">
                                <div className="epl-quick-edit-title">
                                        <strong>{listing.address || listing.title}</strong>
                                        <span className="epl-quick-edit-id">#{listing.id}</span>
                                </div>

                                {error && (
                                        <div className="epl-quick-edit-error">{error}</div>
                                )}

                                <div className="epl-quick-edit-fields">
                                        {/* Render grouped fields */}
                                        {Object.entries(groupedFields.groups).map(([groupName, fields]) =>
                                                renderGroup(groupName, fields)
                                        )}

                                        {/* Render ungrouped fields */}
                                        {groupedFields.ungrouped.map((field) => renderField(field))}
                                </div>
                        </div>

                        <div className="epl-quick-edit-footer">
                                <Button
                                        variant="tertiary"
                                        onClick={onClose}
                                        disabled={saving}
                                >
                                        {__('Cancel', 'easy-property-listings')}
                                </Button>
                                <Button
                                        variant="primary"
                                        onClick={handleSave}
                                        disabled={saving}
                                >
                                        {saving ? (
                                                <>
                                                        <Spinner />
                                                        {__('Saving...', 'easy-property-listings')}
                                                </>
                                        ) : (
                                                __('Save Changes', 'easy-property-listings')
                                        )}
                                </Button>
                        </div>
                </Modal>
        );
}
