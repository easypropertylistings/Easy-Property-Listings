/**
 * EPL Dashboard - AgentLeaderboard Component
 *
 * Clean agent performance display.
 *
 * @package EPL
 * @since 4.0.0
 */

import { __ } from '@wordpress/i18n';

export default function AgentLeaderboard({ agents }) {
        if (!agents || agents.length === 0) {
                return (
                        <div className="epl-widget">
                                <div className="epl-widget__header">
                                        <h3 className="epl-widget__title">
                                                {__('Agent Performance', 'easy-property-listings')}
                                        </h3>
                                </div>
                                <div className="epl-widget__content">
                                        <div className="epl-widget__empty">
                                                {__('No agent data', 'easy-property-listings')}
                                        </div>
                                </div>
                        </div>
                );
        }

        const maxActive = Math.max(...agents.map((a) => a.activeCount), 1);

        return (
                <div className="epl-widget">
                        <div className="epl-widget__header">
                                <h3 className="epl-widget__title">
                                        {__('Agent Performance', 'easy-property-listings')}
                                </h3>
                        </div>
                        <div className="epl-widget__content">
                                <div className="epl-agent-list">
                                        {agents.map((agent, index) => (
                                                <div key={agent.id} className="epl-agent-row">
                                                        <div className="epl-agent-row__rank">{index + 1}</div>
                                                        <img
                                                                src={agent.avatar}
                                                                alt=""
                                                                className="epl-agent-row__avatar"
                                                        />
                                                        <div className="epl-agent-row__info">
                                                                <div className="epl-agent-row__name">{agent.name}</div>
                                                                <div className="epl-agent-row__bar">
                                                                        <div
                                                                                className="epl-agent-row__progress"
                                                                                style={{ width: `${(agent.activeCount / maxActive) * 100}%` }}
                                                                        />
                                                                </div>
                                                        </div>
                                                        <div className="epl-agent-row__stats">
                                                                <div className="epl-agent-row__stat">
                                                                        <span className="epl-agent-row__stat-val">{agent.activeCount}</span>
                                                                        <span className="epl-agent-row__stat-lbl">ACTIVE</span>
                                                                </div>
                                                                <div className="epl-agent-row__stat">
                                                                        <span className="epl-agent-row__stat-val">{agent.soldCount}</span>
                                                                        <span className="epl-agent-row__stat-lbl">SOLD</span>
                                                                </div>
                                                        </div>
                                                </div>
                                        ))}
                                </div>
                        </div>
                </div>
        );
}
