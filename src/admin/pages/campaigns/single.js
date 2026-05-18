/**
 * Single campaign — full-page editor (Autonami /automation/:id style).
 */
import { __ } from '@wordpress/i18n';
import { useCallback, useEffect, useState } from '@wordpress/element';
import { Spinner, TextControl } from '@wordpress/components';
import { useNavigate, useParams } from 'react-router-dom';
import CampaignFullscreenShell from '../../components/campaigns/campaign-fullscreen-shell';
import RulesBuilder from '../../components/campaigns/rules-builder';
import SchemaFieldsPanel from '../../components/campaigns/schema-fields-panel';
import ProFeatureModal from '../../components/campaigns/pro-feature-modal';
import useCampaignFullscreen from '../../hooks/use-campaign-fullscreen';
import { useProUpsell } from '../../hooks/use-pro-upsell';
import { isPro } from '../../utils/is-pro';
import {
	fetchCampaign,
	fetchCampaignTabs,
	fetchRuleTypes,
	saveCampaign,
} from '../../hooks/use-campaigns-api';

export default function CampaignSingle() {
	const { id } = useParams();
	const navigate = useNavigate();
	const campaignId = parseInt( id, 10 );

	useCampaignFullscreen( true );
	const {
		isPro: hasPro,
		openUpsell,
		closeUpsell,
		activeUpsell,
	} = useProUpsell();

	const [ campaign, setCampaign ] = useState( null );
	const [ tabs, setTabs ] = useState( [] );
	const [ ruleTypes, setRuleTypes ] = useState( {
		groups: [],
		definitions: {},
	} );
	const [ activeTab, setActiveTab ] = useState( 'schedule' );
	const [ loading, setLoading ] = useState( true );
	const [ saving, setSaving ] = useState( false );
	const [ error, setError ] = useState( null );
	const [ notice, setNotice ] = useState( '' );

	const load = useCallback( () => {
		if ( ! campaignId || Number.isNaN( campaignId ) ) {
			setError( __( 'Invalid campaign.', 'wp-ext-rule-pricing' ) );
			setLoading( false );
			return;
		}
		setLoading( true );
		setError( null );
		Promise.all( [
			fetchCampaign( campaignId ),
			fetchCampaignTabs(),
			fetchRuleTypes(),
		] )
			.then( ( [ item, tabList, types ] ) => {
				setCampaign( item );
				setTabs( Array.isArray( tabList ) ? tabList : [] );
				setRuleTypes( types || { groups: [], definitions: {} } );
			} )
			.catch( ( err ) =>
				setError(
					err?.message ||
						__( 'Could not load campaign.', 'wp-ext-rule-pricing' )
				)
			)
			.finally( () => setLoading( false ) );
	}, [ campaignId ] );

	useEffect( () => {
		load();
	}, [ load ] );

	const patchCampaign = ( patch ) => {
		setCampaign( ( prev ) => ( { ...prev, ...patch } ) );
	};

	const patchTabSetting = ( tabId, fieldId, value ) => {
		setCampaign( ( prev ) => {
			const settings = { ...( prev?.settings || {} ) };
			const tabSettings = { ...( settings[ tabId ] || {} ) };
			tabSettings[ fieldId ] = value;
			settings[ tabId ] = tabSettings;
			return { ...prev, settings };
		} );
	};

	const handleSave = async () => {
		if ( ! campaign?.id ) {
			return;
		}
		setSaving( true );
		setNotice( '' );
		try {
			const saved = await saveCampaign( campaign.id, campaign );
			setCampaign( saved );
			setNotice( __( 'Saved', 'wp-ext-rule-pricing' ) );
			setTimeout( () => setNotice( '' ), 3000 );
		} catch ( err ) {
			setError(
				err?.message ||
					__( 'Could not save campaign.', 'wp-ext-rule-pricing' )
			);
		} finally {
			setSaving( false );
		}
	};

	const handleClose = () => {
		navigate( '/campaigns' );
	};

	const activeTabDef = tabs.find( ( t ) => t.id === activeTab );

	const isTabProLocked = ( tab ) => {
		if ( hasPro || isPro() ) {
			return false;
		}
		if ( tab.pro ) {
			return true;
		}
		if ( tab.locked && ! tab.sections?.length ) {
			return true;
		}
		return false;
	};

	const handleTabClick = ( tab ) => {
		if ( isTabProLocked( tab ) ) {
			openUpsell( tab.upsell_id || `tab_${ tab.id }` );
			return;
		}
		setActiveTab( tab.id );
	};

	const sidebar = (
		<nav
			className="wpextrulepricing-campaign-fs__nav"
			aria-label={ __( 'Campaign settings', 'wp-ext-rule-pricing' ) }
		>
			<ul>
				{ tabs.map( ( tab ) => (
					<li key={ tab.id }>
						<button
							type="button"
							className={
								activeTab === tab.id
									? 'wpextrulepricing-campaign-fs__nav-item is-active'
									: 'wpextrulepricing-campaign-fs__nav-item'
							}
							onClick={ () => handleTabClick( tab ) }
						>
							{ tab.label }
							{ isTabProLocked( tab ) ? (
								<span className="wpextrulepricing-pro-badge">
									Pro
								</span>
							) : null }
						</button>
					</li>
				) ) }
			</ul>
		</nav>
	);

	const renderTabPanel = () => {
		if ( activeTab === 'rules' ) {
			return (
				<div className="wpextrulepricing-campaign-fs__panel wpextrulepricing-campaign-fs__panel--rules">
					<RulesBuilder
						value={ campaign?.rules }
						ruleTypes={ ruleTypes }
						onChange={ ( rules ) => patchCampaign( { rules } ) }
					/>
				</div>
			);
		}

		if ( activeTabDef?.sections?.length ) {
			return (
				<div className="wpextrulepricing-campaign-fs__panel">
					<SchemaFieldsPanel
						tabId={ activeTab }
						sections={ activeTabDef.sections }
						values={ campaign?.settings?.[ activeTab ] || {} }
						onChange={ ( fieldId, value ) =>
							patchTabSetting( activeTab, fieldId, value )
						}
						onProClick={ openUpsell }
					/>
				</div>
			);
		}

		return (
			<div className="wpextrulepricing-campaign-fs__panel wpextrulepricing-campaign-fs__panel--locked">
				<p className="description">
					{ __( 'No fields for this tab yet.', 'wp-ext-rule-pricing' ) }
				</p>
			</div>
		);
	};

	if ( loading ) {
		return (
			<CampaignFullscreenShell
				title={ __( 'Loading…', 'wp-ext-rule-pricing' ) }
				status="draft"
				onStatusChange={ () => {} }
				onClose={ handleClose }
				onSave={ handleSave }
				saving={ false }
				sidebar={ sidebar }
			>
				<p className="wpextrulepricing-campaign-fs__loading">
					<Spinner />
					{ __( 'Loading campaign…', 'wp-ext-rule-pricing' ) }
				</p>
			</CampaignFullscreenShell>
		);
	}

	if ( error && ! campaign ) {
		return (
			<CampaignFullscreenShell
				title={ __( 'Error', 'wp-ext-rule-pricing' ) }
				status="draft"
				onStatusChange={ () => {} }
				onClose={ handleClose }
				onSave={ handleSave }
				saving={ false }
				sidebar={ sidebar }
			>
				<p className="wpextrulepricing-campaigns__error">{ error }</p>
			</CampaignFullscreenShell>
		);
	}

	const shell = (
		<CampaignFullscreenShell
			title={ campaign?.title }
			status={ campaign?.status || 'draft' }
			onStatusChange={ ( status ) => patchCampaign( { status } ) }
			onClose={ handleClose }
			onSave={ handleSave }
			saving={ saving }
			notice={ notice }
			sidebar={ sidebar }
		>
			<div className="wpextrulepricing-campaign-fs__editor">
				<div className="wpextrulepricing-campaign-fs__editor-bar">
					<TextControl
						label={ __( 'Campaign title', 'wp-ext-rule-pricing' ) }
						value={ campaign?.title || '' }
						onChange={ ( title ) => patchCampaign( { title } ) }
						className="wpextrulepricing-campaign-fs__title-field"
					/>
					<TextControl
						label={ __( 'Priority', 'wp-ext-rule-pricing' ) }
						type="number"
						value={ String( campaign?.priority ?? 10 ) }
						onChange={ ( val ) =>
							patchCampaign( {
								priority: parseInt( val, 10 ) || 10,
							} )
						}
						className="wpextrulepricing-campaign-fs__priority-field"
					/>
				</div>
				{ error ? (
					<p className="wpextrulepricing-campaigns__error">{ error }</p>
				) : null }
				<div className="wpextrulepricing-campaign-fs__settings-card">
					{ renderTabPanel() }
				</div>
			</div>
		</CampaignFullscreenShell>
	);

	return (
		<>
			{ shell }
			<ProFeatureModal
				upsell={ activeUpsell }
				isOpen={ !! activeUpsell }
				onClose={ closeUpsell }
			/>
		</>
	);
}
