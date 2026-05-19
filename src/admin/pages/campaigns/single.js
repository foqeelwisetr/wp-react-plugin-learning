/**
 * Single campaign — full-page editor (Autonami /automation/:id style).
 */
import { __ } from '@wordpress/i18n';
import { useCallback, useEffect, useMemo, useState } from '@wordpress/element';
import classNames from 'classnames';
import { Spinner } from '@wordpress/components';
import { useNavigate, useParams } from 'react-router-dom';
import {
	isTabAccessible,
	resolveActiveTab,
} from '../../utils/resolve-campaign-tab';
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
	const { id, tabId: urlTabId } = useParams();
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
	const [ activeTab, setActiveTab ] = useState( urlTabId || '' );
	const [ loading, setLoading ] = useState( true );
	const [ saving, setSaving ] = useState( false );
	const [ error, setError ] = useState( null );
	const [ notice, setNotice ] = useState( null );

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
				const settings = { ...( item?.settings || {} ) };
				settings.schedule = {
					...( settings.schedule || {} ),
					priority: item?.priority ?? 10,
				};
				setCampaign( { ...item, settings } );
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

	const hasProUnlocked = hasPro || isPro();

	useEffect( () => {
		setActiveTab( urlTabId || '' );
	}, [ campaignId ] );

	useEffect( () => {
		if ( ! tabs.length || ! campaignId ) {
			return;
		}
		const resolved = resolveActiveTab(
			tabs,
			urlTabId || activeTab,
			hasProUnlocked
		);
		if ( resolved !== activeTab ) {
			setActiveTab( resolved );
		}
		if ( urlTabId !== resolved ) {
			navigate( `/campaigns/${ campaignId }/${ resolved }`, {
				replace: true,
			} );
		}
	}, [ tabs, urlTabId, campaignId, hasProUnlocked ] );

	const patchCampaign = ( patch ) => {
		setCampaign( ( prev ) => ( { ...prev, ...patch } ) );
	};

	const patchTabSetting = ( tabId, fieldId, value, fieldDef ) => {
		setCampaign( ( prev ) => {
			const settings = { ...( prev?.settings || {} ) };
			const tabSettings = { ...( settings[ tabId ] || {} ) };
			tabSettings[ fieldId ] = value;
			settings[ tabId ] = tabSettings;
			const next = { ...prev, settings };
			if ( fieldDef?.campaign_meta === 'priority' ) {
				next.priority = parseInt( value, 10 ) || 10;
			}
			return next;
		} );
	};

	const getTabFieldValue = ( tabId, field ) => {
		if ( field?.campaign_meta === 'priority' ) {
			return campaign?.priority ?? campaign?.settings?.[ tabId ]?.priority ?? 10;
		}
		return campaign?.settings?.[ tabId ]?.[ field.id ];
	};

	const handleSave = async () => {
		if ( ! campaign?.id ) {
			return;
		}
		setSaving( true );
		setNotice( null );
		try {
			const payload = { ...campaign };
			const schedulePriority = payload.settings?.schedule?.priority;
			if ( schedulePriority !== undefined && schedulePriority !== '' ) {
				payload.priority = parseInt( schedulePriority, 10 ) || 10;
			}
			const saved = await saveCampaign( campaign.id, payload );
			setCampaign( saved );
			setNotice( {
				status: 'success',
				message: __( 'Saved successfully', 'wp-ext-rule-pricing' ),
			} );
			setTimeout( () => setNotice( null ), 3000 );
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
	const isCanvasTab = useMemo(
		() => activeTab === 'rules' || activeTabDef?.layout === 'canvas',
		[ activeTab, activeTabDef ]
	);

	const isTabProLocked = ( tab ) => ! isTabAccessible( tab, hasProUnlocked );

	const handleTabClick = ( tab ) => {
		if ( isTabProLocked( tab ) ) {
			openUpsell( tab.upsell_id || `tab_${ tab.id }` );
			return;
		}
		setActiveTab( tab.id );
		navigate( `/campaigns/${ campaignId }/${ tab.id }` );
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
		if ( ! activeTab ) {
			return (
				<p className="wpextrulepricing-campaign-fs__loading">
					<Spinner />
				</p>
			);
		}

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
						getFieldValue={ ( field ) =>
							getTabFieldValue( activeTab, field )
						}
						onChange={ ( fieldId, value, field ) =>
							patchTabSetting( activeTab, fieldId, value, field )
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
			onTitleChange={ ( title ) => patchCampaign( { title } ) }
			priority={ campaign?.priority ?? 10 }
			onPriorityChange={ ( priority ) => patchCampaign( { priority } ) }
			status={ campaign?.status || 'draft' }
			onStatusChange={ ( status ) => patchCampaign( { status } ) }
			onClose={ handleClose }
			onSave={ handleSave }
			saving={ saving }
			notice={ notice }
			sidebar={ sidebar }
		>
			<div className="wpextrulepricing-campaign-fs__editor">
				{ activeTabDef?.label ? (
					<div className="wpextrulepricing-campaign-fs__editor-toolbar">
						<h2 className="wpextrulepricing-campaign-fs__tab-heading">
							{ activeTabDef.label }
						</h2>
					</div>
				) : null }
				{ error ? (
					<p className="wpextrulepricing-campaigns__error">{ error }</p>
				) : null }
				<div
					className={ classNames(
						'wpextrulepricing-campaign-fs__editor-body',
						isCanvasTab
							? 'wpextrulepricing-campaign-fs__editor-body--canvas'
							: 'wpextrulepricing-campaign-fs__editor-body--form'
					) }
				>
					<div
						className={
							isCanvasTab
								? 'wpextrulepricing-campaign-fs__canvas-panel'
								: 'wpextrulepricing-campaign-fs__settings-card'
						}
					>
						{ renderTabPanel() }
					</div>
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
