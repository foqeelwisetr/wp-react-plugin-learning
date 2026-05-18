/**
 * Shared layout for campaign list + single (Finale / Autonami style).
 */
import { __ } from '@wordpress/i18n';
import { Button } from '@wordpress/components';
import { AtrcLink, AtrcWrap } from 'atrc';
import classNames from 'classnames';

export default function CampaignShell( {
	title,
	backTo,
	backLabel,
	onClose,
	actions,
	sidebar,
	children,
} ) {
	return (
		<div className="wpextrulepricing-campaigns">
			<div className="wpextrulepricing-campaigns__toolbar">
				<div className="wpextrulepricing-campaigns__toolbar-start">
					{ backTo ? (
						<AtrcLink
							type="router-link"
							to={ backTo }
							className="wpextrulepricing-campaigns__back"
						>
							<span aria-hidden="true">←</span>{ ' ' }
							{ backLabel ||
								__( 'Back to campaigns', 'wp-ext-rule-pricing' ) }
						</AtrcLink>
					) : null }
					{ onClose ? (
						<Button
							variant="link"
							className="wpextrulepricing-campaigns__close"
							onClick={ onClose }
							label={ __( 'Close', 'wp-ext-rule-pricing' ) }
						>
							×
						</Button>
					) : null }
				</div>
				{ title ? (
					<h1 className="wpextrulepricing-campaigns__page-title">{ title }</h1>
				) : null }
				{ actions ? (
					<div className="wpextrulepricing-campaigns__toolbar-actions">
						{ actions }
					</div>
				) : null }
			</div>
			<AtrcWrap
				className={ classNames(
					'wpextrulepricing-campaigns__body',
					sidebar && 'wpextrulepricing-campaigns__body--with-sidebar'
				) }
			>
				{ sidebar ? (
					<aside className="wpextrulepricing-campaigns__sidebar">
						{ sidebar }
					</aside>
				) : null }
				<main className="wpextrulepricing-campaigns__main">{ children }</main>
			</AtrcWrap>
		</div>
	);
}
