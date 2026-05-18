/**
 * Autonami-style delete confirmation modal.
 */
import { __ } from '@wordpress/i18n';
import { Button, Modal } from '@wordpress/components';

export default function DeleteCampaignModal( {
	campaign,
	isOpen,
	isDeleting,
	onCancel,
	onConfirm,
} ) {
	if ( ! isOpen || ! campaign ) {
		return null;
	}

	const title = campaign.title || __( '(no title)', 'wp-ext-rule-pricing' );

	return (
		<Modal
			title={ __( 'Delete Campaign', 'wp-ext-rule-pricing' ) }
			onRequestClose={ onCancel }
			className="wpextrulepricing-delete-modal"
			isDismissible
			shouldCloseOnClickOutside
			shouldCloseOnEsc
		>
			<p className="wpextrulepricing-delete-modal__message">
				{ __( 'You are about to delete', 'wp-ext-rule-pricing' ) }{ ' ' }
				<strong>{ title }</strong>
				{ __(
					'. This action cannot be undone. Cancel to stop, Delete to proceed.',
					'wp-ext-rule-pricing'
				) }
			</p>
			<div className="wpextrulepricing-delete-modal__actions">
				<Button variant="tertiary" onClick={ onCancel } disabled={ isDeleting }>
					{ __( 'Cancel', 'wp-ext-rule-pricing' ) }
				</Button>
				<Button
					className="wpextrulepricing-delete-modal__confirm"
					variant="primary"
					isDestructive
					onClick={ onConfirm }
					isBusy={ isDeleting }
					disabled={ isDeleting }
				>
					{ __( 'Delete', 'wp-ext-rule-pricing' ) }
				</Button>
			</div>
		</Modal>
	);
}
