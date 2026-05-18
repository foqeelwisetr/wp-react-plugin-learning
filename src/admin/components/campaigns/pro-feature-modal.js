/**
 * FunnelKit-style Pro upsell modal — all copy from PHP upsell registry.
 */
import { __ } from '@wordpress/i18n';
import { Button, Modal } from '@wordpress/components';

function UpsellPreview( { preview } ) {
	if ( ! preview || ! preview.type ) {
		return null;
	}

	if ( preview.type === 'table' && preview.columns?.length ) {
		return (
			<div className="wpextrulepricing-pro-feature-modal__preview">
				<table className="wpextrulepricing-pro-feature-modal__table">
					<thead>
						<tr>
							{ preview.columns.map( ( col, i ) => (
								<th key={ i }>{ col }</th>
							) ) }
						</tr>
					</thead>
					<tbody>
						{ ( preview.rows || [] ).map( ( row, ri ) => (
							<tr key={ ri }>
								{ row.map( ( cell, ci ) => (
									<td key={ ci }>{ cell }</td>
								) ) }
							</tr>
						) ) }
					</tbody>
				</table>
			</div>
		);
	}

	const items = preview.items || preview.rows || [];
	if ( ! items.length ) {
		return null;
	}

	return (
		<div className="wpextrulepricing-pro-feature-modal__preview">
			<ul className="wpextrulepricing-pro-feature-modal__list">
				{ items.map( ( item, i ) => (
					<li key={ i }>{ item }</li>
				) ) }
			</ul>
		</div>
	);
}

export default function ProFeatureModal( { upsell, isOpen, onClose } ) {
	if ( ! isOpen || ! upsell ) {
		return null;
	}

	const title = upsell.title || __( 'Pro Feature', 'wp-ext-rule-pricing' );
	const upgradeUrl = upsell.upgrade_url || '#';

	return (
		<Modal
			title={
				<span className="wpextrulepricing-pro-feature-modal__title">
					{ title }
					<span className="wpextrulepricing-pro-crown" aria-hidden>
						👑
					</span>
				</span>
			}
			onRequestClose={ onClose }
			className="wpextrulepricing-pro-feature-modal"
			isDismissible
		>
			<UpsellPreview preview={ upsell.preview } />
			{ upsell.headline ? (
				<p className="wpextrulepricing-pro-feature-modal__headline">
					{ upsell.headline }
				</p>
			) : null }
			<div className="wpextrulepricing-pro-feature-modal__cta-wrap">
				<Button
					className="wpextrulepricing-pro-feature-modal__cta"
					variant="primary"
					href={ upgradeUrl }
					target="_blank"
					rel="noopener noreferrer"
				>
					<span className="wpextrulepricing-pro-crown" aria-hidden>
						👑
					</span>
					{ upsell.button_text || __( 'Upgrade to PRO', 'wp-ext-rule-pricing' ) }
				</Button>
			</div>
			{ upsell.footnote ? (
				<p className="wpextrulepricing-pro-feature-modal__footnote description">
					<span aria-hidden>✓</span> { upsell.footnote }
				</p>
			) : null }
		</Modal>
	);
}
