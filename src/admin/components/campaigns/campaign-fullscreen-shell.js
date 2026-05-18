/**
 * Full-page campaign editor shell (Autonami workflow style).
 */
import { __ } from '@wordpress/i18n';
import { Button, ToggleControl } from '@wordpress/components';

export default function CampaignFullscreenShell( {
	title,
	status,
	onStatusChange,
	onClose,
	onSave,
	saving,
	notice,
	sidebar,
	children,
} ) {
	const isActive = status === 'active';

	return (
		<div className="wpextrulepricing-campaign-fs">
			<header className="wpextrulepricing-campaign-fs__header">
				<div className="wpextrulepricing-campaign-fs__header-left">
					<h1 className="wpextrulepricing-campaign-fs__title">
						{ title || __( 'Untitled campaign', 'wp-ext-rule-pricing' ) }
					</h1>
				</div>
				<div className="wpextrulepricing-campaign-fs__header-right">
					{ notice ? (
						<span className="wpextrulepricing-campaign-fs__notice">
							{ notice }
						</span>
					) : null }
					<div className="wpextrulepricing-campaign-fs__status">
						<span className="wpextrulepricing-campaign-fs__status-label">
							{ isActive
								? __( 'Active', 'wp-ext-rule-pricing' )
								: __( 'Inactive', 'wp-ext-rule-pricing' ) }
						</span>
						<ToggleControl
							className="wpextrulepricing-campaign-fs__status-toggle"
							checked={ isActive }
							onChange={ ( checked ) =>
								onStatusChange( checked ? 'active' : 'paused' )
							}
							__nextHasNoMarginBottom
						/>
					</div>
					<Button
						variant="primary"
						onClick={ onSave }
						isBusy={ saving }
						disabled={ saving }
					>
						{ __( 'Update', 'wp-ext-rule-pricing' ) }
					</Button>
					<Button
						variant="tertiary"
						className="wpextrulepricing-campaign-fs__close"
						onClick={ onClose }
						label={ __( 'Close', 'wp-ext-rule-pricing' ) }
					>
						×
					</Button>
				</div>
			</header>
			<div className="wpextrulepricing-campaign-fs__layout">
				{ sidebar ? (
					<aside className="wpextrulepricing-campaign-fs__sidebar">
						{ sidebar }
					</aside>
				) : null }
				<main className="wpextrulepricing-campaign-fs__main">
					<div className="wpextrulepricing-campaign-fs__canvas">
						{ children }
					</div>
				</main>
			</div>
		</div>
	);
}
