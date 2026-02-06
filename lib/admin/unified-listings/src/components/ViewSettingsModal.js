/**
 * EPL Unified Listings - ViewSettingsModal Component
 *
 * @package EPL
 * @since 4.0.0
 */

import { useState, useCallback } from '@wordpress/element';
import {
        Modal,
        Button,
        RangeControl,
        ToggleControl,
        SelectControl,
        Flex,
        FlexItem,
        TabPanel,
        Icon,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { chevronUp, chevronDown } from '@wordpress/icons';

import { useConfig } from '../store';

// Available columns configuration
const allOptionColumns = [
        { key: 'id', label: __('ID', 'easy-property-listings') },
        { key: 'thumbnail', label: __('Image', 'easy-property-listings') },
        { key: 'title', label: __('Title', 'easy-property-listings') },
        { key: 'heading', label: __('Heading', 'easy-property-listings') },
        { key: 'address', label: __('Address', 'easy-property-listings') },
        { key: 'suburb', label: __('Suburb', 'easy-property-listings') },
        { key: 'listingId', label: __('Listing ID', 'easy-property-listings') },
        { key: 'postType', label: __('Type', 'easy-property-listings') },
        { key: 'propertyStatus', label: __('Status', 'easy-property-listings') },
        { key: 'priceDisplay', label: __('Price', 'easy-property-listings') },
        { key: 'bedrooms', label: __('Beds', 'easy-property-listings') },
        { key: 'bathrooms', label: __('Baths', 'easy-property-listings') },
        { key: 'featured', label: __('Featured', 'easy-property-listings') },
        { key: 'features', label: __('Features', 'easy-property-listings') },
        { key: 'author', label: __('Author', 'easy-property-listings') },
        { key: 'agent', label: __('Agent', 'easy-property-listings') },
        { key: 'date', label: __('Date', 'easy-property-listings') },
        { key: 'modified', label: __('Modified', 'easy-property-listings') },
];

const compactOptionFields = [
        { key: 'id', label: __('ID', 'easy-property-listings') },
        {
                key: 'address',
                label: __('Address / Title', 'easy-property-listings'),
        },
        { key: 'priceDisplay', label: __('Price', 'easy-property-listings') },
        { key: 'propertyStatus', label: __('Status', 'easy-property-listings') },
        { key: 'modified', label: __('Modified', 'easy-property-listings') },
        { key: 'date', label: __('Date', 'easy-property-listings') },
        { key: 'bedrooms', label: __('Beds', 'easy-property-listings') },
        { key: 'bathrooms', label: __('Baths', 'easy-property-listings') },
        { key: 'postType', label: __('Type', 'easy-property-listings') },
];

/**
 * Reorderable Column List Component
 */
const ReorderableColumnList = ({
        activeKeys,
        allOptions,
        onChange,
        labelActive,
        labelAvailable,
}) => {
        // Separate active and available objects
        const activeItems = activeKeys
                .map((key) => allOptions.find((col) => col.key === key))
                .filter(Boolean);

        const availableItems = allOptions.filter(
                (col) => !activeKeys.includes(col.key)
        );

        const handleMove = (index, direction) => {
                const newKeys = [...activeKeys];
                if (direction === 'up' && index > 0) {
                        [newKeys[index - 1], newKeys[index]] = [
                                newKeys[index],
                                newKeys[index - 1],
                        ];
                } else if (direction === 'down' && index < newKeys.length - 1) {
                        [newKeys[index + 1], newKeys[index]] = [
                                newKeys[index],
                                newKeys[index + 1],
                        ];
                }
                onChange(newKeys);
        };

        const handleToggle = (key, checked) => {
                let newKeys;
                if (checked) {
                        newKeys = [...activeKeys, key];
                } else {
                        newKeys = activeKeys.filter((k) => k !== key);
                }
                onChange(newKeys);
        };

        return (
                <div className="epl-reorder-list">
                        { /* Active Columns */}
                        {activeItems.length > 0 && (
                                <div className="epl-active-columns">
                                        <h4>{labelActive}</h4>
                                        {activeItems.map((col, index) => (
                                                <div key={col.key} className="epl-reorder-row">
                                                        <div className="epl-reorder-controls">
                                                                <Button
                                                                        icon={chevronUp}
                                                                        size="small"
                                                                        disabled={index === 0}
                                                                        onClick={() => handleMove(index, 'up')}
                                                                        label={__(
                                                                                'Move Up',
                                                                                'easy-property-listings'
                                                                        )}
                                                                />
                                                                <Button
                                                                        icon={chevronDown}
                                                                        size="small"
                                                                        disabled={
                                                                                index === activeItems.length - 1
                                                                        }
                                                                        onClick={() =>
                                                                                handleMove(index, 'down')
                                                                        }
                                                                        label={__(
                                                                                'Move Down',
                                                                                'easy-property-listings'
                                                                        )}
                                                                />
                                                        </div>
                                                        <div className="epl-reorder-label">
                                                                {col.label}
                                                        </div>
                                                        <div className="epl-reorder-toggle">
                                                                <ToggleControl
                                                                        checked={true}
                                                                        onChange={() =>
                                                                                handleToggle(col.key, false)
                                                                        }
                                                                />
                                                        </div>
                                                </div>
                                        ))}
                                </div>
                        )}

                        { /* Available Columns */}
                        {availableItems.length > 0 && (
                                <div className="epl-available-columns">
                                        <h4>{labelAvailable}</h4>
                                        {availableItems.map((col) => (
                                                <div key={col.key} className="epl-reorder-row">
                                                        <div className="epl-reorder-controls epl-reorder-controls--placeholder" />
                                                        <div className="epl-reorder-label">
                                                                {col.label}
                                                        </div>
                                                        <div className="epl-reorder-toggle">
                                                                <ToggleControl
                                                                        checked={false}
                                                                        onChange={() =>
                                                                                handleToggle(col.key, true)
                                                                        }
                                                                />
                                                        </div>
                                                </div>
                                        ))}
                                </div>
                        )}
                </div>
        );
};

/**
 * List View Settings Panel
 */
const ListViewSettings = ({
        localSettings,
        onColumnsChange,
        onPerPageChange,
        onImageSizeChange,
        imageSizes = [],
}) => (
        <div className="epl-view-settings-panel">
                <h3>{__('Visible Columns', 'easy-property-listings')}</h3>
                <ReorderableColumnList
                        activeKeys={localSettings.list?.columns || []}
                        allOptions={allOptionColumns}
                        onChange={(newKeys) => onColumnsChange('list', newKeys)}
                        labelActive={__(
                                'Active Columns (Reorder)',
                                'easy-property-listings'
                        )}
                        labelAvailable={__(
                                'Available Columns',
                                'easy-property-listings'
                        )}
                />

                <h3>{__('Thumbnail Size', 'easy-property-listings')}</h3>
                <SelectControl
                        value={localSettings.list?.imageSize || 'thumbnail'}
                        options={imageSizes.length > 0 ? imageSizes : [
                                { value: 'thumbnail', label: 'Thumbnail' },
                                { value: 'medium', label: 'Medium' },
                                { value: 'large', label: 'Large' },
                        ]}
                        onChange={(value) => onImageSizeChange('list', value)}
                />

                <h3>{__('Items Per Page', 'easy-property-listings')}</h3>
                <RangeControl
                        value={localSettings.list?.perPage || 25}
                        onChange={(value) => onPerPageChange('list', value)}
                        min={10}
                        max={100}
                        step={5}
                />
        </div>
);

/**
 * Grid View Settings Panel
 */
const GridViewSettings = ({ localSettings, onGridChange }) => (
        <div className="epl-view-settings-panel">
                <h3>{__('Grid Layout', 'easy-property-listings')}</h3>
                <RangeControl
                        label={__('Cards Per Row', 'easy-property-listings')}
                        value={localSettings.grid?.cardsPerRow || 4}
                        onChange={(value) => onGridChange('cardsPerRow', value)}
                        min={2}
                        max={6}
                />

                <h3>{__('Card Information', 'easy-property-listings')}</h3>
                <ToggleControl
                        label={__('Show Price', 'easy-property-listings')}
                        checked={localSettings.grid?.showPrice !== false}
                        onChange={(value) => onGridChange('showPrice', value)}
                />
                <ToggleControl
                        label={__('Show Status', 'easy-property-listings')}
                        checked={localSettings.grid?.showStatus !== false}
                        onChange={(value) => onGridChange('showStatus', value)}
                />
                <ToggleControl
                        label={__('Show Bedrooms', 'easy-property-listings')}
                        checked={localSettings.grid?.showBeds !== false}
                        onChange={(value) => onGridChange('showBeds', value)}
                />
                <ToggleControl
                        label={__('Show Bathrooms', 'easy-property-listings')}
                        checked={localSettings.grid?.showBaths !== false}
                        onChange={(value) => onGridChange('showBaths', value)}
                />
        </div>
);

/**
 * Compact View Settings Panel
 */
const CompactViewSettings = ({
        localSettings,
        onFieldsChange,
        onPerPageChange,
}) => (
        <div className="epl-view-settings-panel">
                <h3>{__('Visible Fields', 'easy-property-listings')}</h3>
                <ReorderableColumnList
                        activeKeys={localSettings.compact?.fields || []}
                        allOptions={compactOptionFields}
                        onChange={(newKeys) => onFieldsChange('compact', newKeys)}
                        labelActive={__(
                                'Active Fields (Reorder)',
                                'easy-property-listings'
                        )}
                        labelAvailable={__(
                                'Available Fields',
                                'easy-property-listings'
                        )}
                />

                <h3>{__('Items Per Page', 'easy-property-listings')}</h3>
                <RangeControl
                        value={localSettings.compact?.perPage || 50}
                        onChange={(value) => onPerPageChange('compact', value)}
                        min={25}
                        max={200}
                        step={25}
                />
        </div>
);

/**
 * ViewSettingsModal Component
 */
export default function ViewSettingsModal({
        viewMode,
        settings,
        onClose,
        onSave,
}) {
        const config = useConfig();
        const labels = config.labels || {};

        // Local state for editing
        const [localSettings, setLocalSettings] = useState({ ...settings });

        // Handle columns/fields change (shared logic)
        const handleArrayChange = useCallback(
                (viewKey, newKeys) => {
                        setLocalSettings({
                                ...localSettings,
                                [viewKey]: {
                                        ...localSettings[viewKey],
                                        [viewKey === 'list' ? 'columns' : 'fields']: newKeys,
                                },
                        });
                },
                [localSettings]
        );

        // Handle grid settings change
        const handleGridChange = useCallback(
                (key, value) => {
                        setLocalSettings({
                                ...localSettings,
                                grid: { ...localSettings.grid, [key]: value },
                        });
                },
                [localSettings]
        );

        // Handle per page change
        const handlePerPageChange = useCallback(
                (viewKey, value) => {
                        setLocalSettings({
                                ...localSettings,
                                [viewKey]: { ...localSettings[viewKey], perPage: value },
                        });
                },
                [localSettings]
        );

        // Handle image size change
        const handleImageSizeChange = useCallback(
                (viewKey, value) => {
                        setLocalSettings({
                                ...localSettings,
                                [viewKey]: { ...localSettings[viewKey], imageSize: value },
                        });
                },
                [localSettings]
        );

        // Handle save
        const handleSave = useCallback(() => {
                onSave(localSettings);
                onClose();
        }, [localSettings, onSave, onClose]);

        const tabs = [
                {
                        name: 'list',
                        title:
                                labels.listView || __('List View', 'easy-property-listings'),
                        className: 'epl-settings-tab',
                },
                {
                        name: 'grid',
                        title:
                                labels.gridView || __('Grid View', 'easy-property-listings'),
                        className: 'epl-settings-tab',
                },
                {
                        name: 'compact',
                        title:
                                labels.compactView ||
                                __('Compact View', 'easy-property-listings'),
                        className: 'epl-settings-tab',
                },
        ];

        return (
                <Modal
                        title={
                                labels.settings ||
                                __('View Settings', 'easy-property-listings')
                        }
                        onRequestClose={onClose}
                        className="epl-view-settings-modal"
                >
                        <TabPanel tabs={tabs}>
                                {(tab) => {
                                        switch (tab.name) {
                                                case 'list':
                                                        return (
                                                                <ListViewSettings
                                                                        localSettings={localSettings}
                                                                        onColumnsChange={handleArrayChange}
                                                                        onPerPageChange={handlePerPageChange}
                                                                        onImageSizeChange={handleImageSizeChange}
                                                                        imageSizes={config.imageSizes || []}
                                                                />
                                                        );
                                                case 'grid':
                                                        return (
                                                                <GridViewSettings
                                                                        localSettings={localSettings}
                                                                        onGridChange={handleGridChange}
                                                                />
                                                        );
                                                case 'compact':
                                                        return (
                                                                <CompactViewSettings
                                                                        localSettings={localSettings}
                                                                        onFieldsChange={handleArrayChange}
                                                                        onPerPageChange={handlePerPageChange}
                                                                />
                                                        );
                                                default:
                                                        return null;
                                        }
                                }}
                        </TabPanel>

                        <div className="epl-modal-footer">
                                <Flex justify="flex-end" gap={2}>
                                        <FlexItem>
                                                <Button variant="tertiary" onClick={onClose}>
                                                        {labels.cancel ||
                                                                __('Cancel', 'easy-property-listings')}
                                                </Button>
                                        </FlexItem>
                                        <FlexItem>
                                                <Button variant="primary" onClick={handleSave}>
                                                        {labels.save ||
                                                                __(
                                                                        'Save Settings',
                                                                        'easy-property-listings'
                                                                )}
                                                </Button>
                                        </FlexItem>
                                </Flex>
                        </div>
                </Modal>
        );
}
