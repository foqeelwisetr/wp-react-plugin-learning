/**
 * Pro upsell modal (Coupons, locked fields, etc.).
 */
import { __ } from '@wordpress/i18n';
import { Button, Modal } from '@wordpress/components';

export default function ProUpgradeModal( { isOpen, onClose, title, description } ) {
	if ( ! isOpen ) {
		return null;
	}

	return (
		<Modal
			title={ title || __( 'Upgrade to Pro', 'wp-ext-rule-pricing' ) }
			onRequestClose={ onClose }
			className="wpextrulepricing-pro-modal"
		>
			<p>
				{ description ||
					__(
						'This feature is available in the Pro version. Add fields in PHP with pro => true or register via wp_ext_rule_pricing_is_pro filter.',
						'wp-ext-rule-pricing'
					) }
			</p>
			<p className="description">
				{ __(
					'Lite boilerplate: set add_filter( "wp_ext_rule_pricing_is_pro", "__return_true" ); to unlock all Pro UI for testing.',
					'wp-ext-rule-pricing'
				) }
			</p>
			<div className="wpextrulepricing-pro-modal__actions">
				<Button variant="primary" onClick={ onClose }>
					{ __( 'Close', 'wp-ext-rule-pricing' ) }
				</Button>
			</div>
		</Modal>
	);
}
