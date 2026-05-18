/**
 * Autonami-style delete confirmation modal.
 */
import { __ } from '@wordpress/i18n';
import { Button, Modal } from '@wordpress/components';

export default function DeleteCampaignModal( {
	campaign,
	bulkCount = 0,
	isOpen,
	isDeleting,
	onCancel,
	onConfirm,
} ) {
	if ( ! isOpen || ( ! campaign && bulkCount < 1 ) ) {
		return null;
	}

	const title = campaign?.title || __( '(no title)', 'wp-ext-rule-pricing' );
	const isBulk = bulkCount > 0;

	return (
		<Modal
			title={
				isBulk
					? __( 'Delete Campaigns', 'wp-ext-rule-pricing' )
					: __( 'Delete Campaign', 'wp-ext-rule-pricing' )
			}
			onRequestClose={ onCancel }
			className="wpextrulepricing-delete-modal"
			isDismissible
			shouldCloseOnClickOutside
			shouldCloseOnEsc
		>
			<p className="wpextrulepricing-delete-modal__message">
				{ isBulk ? (
					<>
						{ __( 'You are about to delete', 'wp-ext-rule-pricing' ) }{ ' ' }
						<strong>{ bulkCount }</strong>{ ' ' }
						{ __( 'campaigns', 'wp-ext-rule-pricing' ) }
					</>
				) : (
					<>
						{ __( 'You are about to delete', 'wp-ext-rule-pricing' ) }{ ' ' }
						<strong>{ title }</strong>
					</>
				) }
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
