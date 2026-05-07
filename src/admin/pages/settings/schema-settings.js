/**
 * PHP-driven settings UI (sidebar tabs + sections). Data from GET /settings-ui.
 */
import { __ } from '@wordpress/i18n';
import {
	useCallback,
	useEffect,
	useMemo,
	useRef,
	useState,
} from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';
import {
	Button,
	Notice,
	TextControl,
	TextareaControl,
	ToggleControl,
	SelectControl,
	CheckboxControl,
	RadioControl,
	BaseControl,
} from '@wordpress/components';
import { MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { useParams } from 'react-router-dom';

const SETTINGS_UI_PATH = 'wp-ext-rule-pricing/v1/settings-ui';

let apiMiddlewareReady = false;

/**
 * @param {unknown} raw
 * @returns {{ key: string, label: string }[]}
 */
function normalizeSearchTokens( raw ) {
	if ( ! Array.isArray( raw ) ) {
		return [];
	}
	const out = [];
	for ( const item of raw ) {
		if ( item && typeof item === 'object' && item.key !== undefined ) {
			const key = String( item.key );
			const label =
				item.label !== undefined ? String( item.label ) : key;
			if ( key && label ) {
				out.push( { key, label } );
			}
		} else if ( item !== null && item !== undefined ) {
			const s = String( item );
			if ( s ) {
				out.push( { key: s, label: s } );
			}
		}
	}
	return out;
}

/**
 * Searchable multi-select + removable pills (options from schema).
 *
 * @param {object} props
 */
function SearchMultiselectField( { field, value, onChange } ) {
	const selected = useMemo( () => normalizeSearchTokens( value ), [ value ] );
	const [ query, setQuery ] = useState( '' );
	const [ open, setOpen ] = useState( false );
	const wrapRef = useRef( null );
	const allowFree = !! field.allow_free_text;

	const selectedKeys = useMemo(
		() => new Set( selected.map( ( t ) => t.key.toLowerCase() ) ),
		[ selected ]
	);

	const suggestions = useMemo( () => {
		const q = query.trim().toLowerCase();
		return ( field.options || [] )
			.filter(
				( o ) => ! selectedKeys.has( String( o.value ).toLowerCase() )
			)
			.filter(
				( o ) =>
					! q ||
					String( o.label || '' )
						.toLowerCase()
						.includes( q )
			)
			.map( ( o ) => ( { key: String( o.value ), label: o.label } ) );
	}, [ field.options, query, selectedKeys ] );

	useEffect( () => {
		function onDoc( e ) {
			if (
				wrapRef.current &&
				! wrapRef.current.contains( e.target )
			) {
				setOpen( false );
			}
		}
		document.addEventListener( 'mousedown', onDoc );
		return () => document.removeEventListener( 'mousedown', onDoc );
	}, [] );

	const addToken = ( token ) => {
		const k = String( token.key );
		const l = String( token.label || token.key );
		if ( selectedKeys.has( k.toLowerCase() ) ) {
			return;
		}
		onChange( [ ...selected, { key: k, label: l } ] );
		setQuery( '' );
		setOpen( false );
	};

	const removeToken = ( key ) => {
		onChange( selected.filter( ( t ) => t.key !== key ) );
	};

	const onKeyDown = ( e ) => {
		if ( e.key !== 'Enter' ) {
			return;
		}
		e.preventDefault();
		const q = query.trim();
		if ( ! q || ! allowFree ) {
			return;
		}
		addToken( { key: `free:${ q }`, label: q } );
	};

	const placeholder =
		field.placeholder || __( 'Search by name', 'wp-ext-rule-pricing' );

	const showDropdown = open && suggestions.length > 0;

	return (
		<BaseControl
			className="wpextrulepricing-search-multi"
			label={ field.label }
			help={ field.description }
		>
			<div ref={ wrapRef } className="wpextrulepricing-search-multi__wrap">
				<div className="wpextrulepricing-search-multi__input-row">
					<span
						className="wpextrulepricing-search-multi__icon dashicons dashicons-search"
						aria-hidden
					/>
					<input
						type="text"
						className="wpextrulepricing-search-multi__input"
						value={ query }
						placeholder={ placeholder }
						onChange={ ( e ) => {
							setQuery( e.target.value );
							setOpen( true );
						} }
						onFocus={ () => setOpen( true ) }
						onKeyDown={ onKeyDown }
						autoComplete="off"
					/>
				</div>
				{ showDropdown ? (
					<ul
						className="wpextrulepricing-search-multi__dropdown"
						role="listbox"
					>
						{ suggestions.map( ( s ) => (
							<li key={ s.key } role="none">
								<button
									type="button"
									className="wpextrulepricing-search-multi__suggest"
									onClick={ () => addToken( s ) }
								>
									{ s.label }
								</button>
							</li>
						) ) }
					</ul>
				) : null }
				<div className="wpextrulepricing-search-multi__pills">
					{ selected.map( ( t ) => (
						<span
							key={ t.key }
							className="wpextrulepricing-search-multi__pill"
						>
							<span className="wpextrulepricing-search-multi__pill-label">
								{ t.label }
							</span>
							<button
								type="button"
								className="wpextrulepricing-search-multi__pill-remove"
								onClick={ () => removeToken( t.key ) }
								aria-label={ __( 'Remove', 'wp-ext-rule-pricing' ) }
							>
								×
							</button>
						</span>
					) ) }
				</div>
			</div>
		</BaseControl>
	);
}

function setupApiFetch() {
	if ( apiMiddlewareReady || typeof wpextrulepricingLocalize === 'undefined' ) {
		return;
	}
	const root = wpextrulepricingLocalize.rest_url || '/wp-json/';
	apiFetch.use( apiFetch.createRootURLMiddleware( root ) );
	apiFetch.use( apiFetch.createNonceMiddleware( wpextrulepricingLocalize.nonce ) );
	apiMiddlewareReady = true;
}

function SchemaField( { field, value, onChange } ) {
	const commonNext = {
		__next40pxDefaultSize: true,
		__nextHasNoMarginBottom: true,
	};

	switch ( field.type ) {
		case 'textarea':
			return (
				<TextareaControl
					label={ field.label }
					help={ field.description }
					value={ value ?? '' }
					rows={ field.rows || 4 }
					onChange={ onChange }
					{ ...commonNext }
				/>
			);
		case 'email':
		case 'url':
		case 'text':
			return (
				<TextControl
					type={ field.type === 'email' ? 'email' : field.type === 'url' ? 'url' : 'text' }
					label={ field.label }
					help={ field.description }
					placeholder={ field.placeholder }
					value={ value ?? '' }
					onChange={ onChange }
					{ ...commonNext }
				/>
			);
		case 'password':
			return (
				<TextControl
					type="password"
					label={ field.label }
					help={ field.description }
					value={ value ?? '' }
					onChange={ onChange }
					{ ...commonNext }
				/>
			);
		case 'number':
			return (
				<TextControl
					type="number"
					label={ field.label }
					help={ field.description }
					value={ String( value ?? '' ) }
					min={ field.min }
					max={ field.max }
					step={ field.step }
					onChange={ ( v ) => onChange( v === '' ? '' : Number( v ) ) }
					{ ...commonNext }
				/>
			);
		case 'toggle':
			return (
				<ToggleControl
					label={ field.label }
					help={ field.description }
					checked={ !! value }
					onChange={ onChange }
				/>
			);
		case 'select':
			return (
				<SelectControl
					label={ field.label }
					help={ field.description }
					value={ value ?? '' }
					options={ ( field.options || [] ).map( ( o ) => ( {
						label: o.label,
						value: o.value,
					} ) ) }
					onChange={ onChange }
					{ ...commonNext }
				/>
			);
		case 'radio':
			return (
				<BaseControl label={ field.label } help={ field.description }>
					<RadioControl
						selected={ value ?? '' }
						options={ ( field.options || [] ).map( ( o ) => ( {
							label: o.label,
							value: o.value,
						} ) ) }
						onChange={ onChange }
					/>
				</BaseControl>
			);
		case 'checkbox_group': {
			const selected = Array.isArray( value ) ? value : [];
			return (
				<BaseControl label={ field.label } help={ field.description }>
					{ ( field.options || [] ).map( ( o ) => (
						<CheckboxControl
							key={ o.value }
							label={ o.label }
							checked={ selected.includes( o.value ) }
							onChange={ ( checked ) => {
								if ( checked ) {
									onChange( [ ...selected, o.value ] );
								} else {
									onChange(
										selected.filter( ( x ) => x !== o.value )
									);
								}
							} }
						/>
					) ) }
				</BaseControl>
			);
		}
		case 'search_multiselect':
			return (
				<SearchMultiselectField
					field={ field }
					value={ value }
					onChange={ onChange }
				/>
			);
		case 'multiselect': {
			const selected = Array.isArray( value ) ? value : [];
			return (
				<BaseControl label={ field.label } help={ field.description }>
					<select
						className="wpextrulepricing-settings-ui__multiselect"
						multiple
						value={ selected }
						onChange={ ( e ) => {
							const opts = Array.from(
								e.target.selectedOptions,
								( o ) => o.value
							);
							onChange( opts );
						} }
						size={ Math.min(
							8,
							Math.max( 3, ( field.options || [] ).length )
						) }
					>
						{ ( field.options || [] ).map( ( o ) => (
							<option key={ o.value } value={ o.value }>
								{ o.label }
							</option>
						) ) }
					</select>
				</BaseControl>
			);
		}
		case 'color':
			return (
				<BaseControl label={ field.label } help={ field.description }>
					<input
						type="color"
						className="wpextrulepricing-settings-ui__color"
						value={ value && /^#[0-9A-Fa-f]{6}$/.test( value ) ? value : '#000000' }
						onChange={ ( e ) => onChange( e.target.value ) }
					/>
					<TextControl
						label={ __( 'Hex', 'wp-ext-rule-pricing' ) }
						value={ value ?? '' }
						onChange={ onChange }
						{ ...commonNext }
					/>
				</BaseControl>
			);
		case 'image': {
			const imgId = value ? parseInt( value, 10 ) : 0;
			return (
				<BaseControl label={ field.label } help={ field.description }>
					<MediaUploadCheck>
						<MediaUpload
							onSelect={ ( media ) => onChange( media.id ) }
							allowedTypes={ [ 'image' ] }
							value={ imgId || undefined }
							render={ ( { open } ) => (
								<div className="wpextrulepricing-settings-ui__image">
									{ imgId ? (
										<p className="description">
											{ __( 'Attachment ID:', 'wp-ext-rule-pricing' ) }{ ' ' }
											{ imgId }
										</p>
									) : null }
									<Button variant="secondary" onClick={ open }>
										{ __( 'Select image', 'wp-ext-rule-pricing' ) }
									</Button>
									{ imgId ? (
										<Button
											isDestructive
											variant="link"
											onClick={ () => onChange( 0 ) }
										>
											{ __( 'Remove', 'wp-ext-rule-pricing' ) }
										</Button>
									) : null }
								</div>
							) }
						/>
					</MediaUploadCheck>
				</BaseControl>
			);
		}
		case 'hidden':
			return null;
		case 'link_buttons':
			return (
				<BaseControl
					label={ field.label || undefined }
					help={ field.description }
				>
					<div className="wpextrulepricing-settings-ui__link-buttons">
						{ ( field.buttons || [] ).map( ( btn, idx ) => (
							<Button
								key={ `${ btn.label }-${ idx }` }
								variant="secondary"
								href={ btn.url || '#' }
								target={
									btn.opens_in_new_tab ? '_blank' : undefined
								}
								rel={
									btn.opens_in_new_tab
										? 'noopener noreferrer'
										: undefined
								}
							>
								{ btn.label }
							</Button>
						) ) }
					</div>
				</BaseControl>
			);
		case 'html': {
			const block = (
				<div
					className="wpextrulepricing-settings-ui__html"
					dangerouslySetInnerHTML={ { __html: field.content || '' } }
				/>
			);
			if ( field.label ) {
				return (
					<BaseControl label={ field.label }>{ block }</BaseControl>
				);
			}
			return block;
		}
		default:
			return (
				<TextControl
					label={ field.label }
					help={ field.description }
					value={ value ?? '' }
					onChange={ onChange }
					{ ...commonNext }
				/>
			);
	}
}

function TabPanel( { tab, values, onFieldChange } ) {
	const subsections =
		Array.isArray( tab?.subsections ) && tab.subsections.length > 0
			? tab.subsections
			: null;

	const [ activeSubId, setActiveSubId ] = useState(
		() => subsections?.[ 0 ]?.id || ''
	);

	const subsectionIdsKey = useMemo( () => {
		if ( ! Array.isArray( tab?.subsections ) ) {
			return '';
		}
		return tab.subsections.map( ( s ) => String( s.id ?? '' ) ).join( '|' );
	}, [ tab?.subsections ] );

	useEffect( () => {
		if (
			! Array.isArray( tab?.subsections ) ||
			! tab.subsections.length
		) {
			return;
		}
		const subs = tab.subsections;
		const ids = subs.map( ( s ) => s.id );
		setActiveSubId( ( prev ) =>
			ids.includes( prev ) ? prev : subs[ 0 ].id
		);
	}, [ tab?.slug, subsectionIdsKey ] );

	if ( ! tab ) {
		return (
			<Notice status="warning" isDismissible={ false }>
				{ __( 'Unknown settings tab.', 'wp-ext-rule-pricing' ) }
			</Notice>
		);
	}

	const sectionsToRender = subsections
		? subsections.find( ( s ) => s.id === activeSubId )?.sections ??
		  []
		: tab.sections ?? [];

	return (
		<div className="wpextrulepricing-settings-ui__main">
			<h1 className="wpextrulepricing-settings-ui__page-title">
				{ tab.label }
			</h1>
			{ subsections ? (
				<div
					className="wpextrulepricing-settings-ui__subsection-tabs"
					role="tablist"
					aria-label={ __( 'Panel sections', 'wp-ext-rule-pricing' ) }
				>
					{ subsections.map( ( sub ) => (
						<button
							key={ sub.id }
							type="button"
							className={
								'wpextrulepricing-settings-ui__subsection-tab' +
								( activeSubId === sub.id ? ' is-active' : '' )
							}
							role="tab"
							aria-selected={ activeSubId === sub.id }
							onClick={ () => setActiveSubId( sub.id ) }
						>
							{ sub.label || sub.id }
						</button>
					) ) }
				</div>
			) : null }
			{ sectionsToRender.map( ( section ) => (
				<div
					key={ section.id }
					className="wpextrulepricing-settings-ui__section"
				>
					{ section.title ? (
						<h2 className="wpextrulepricing-settings-ui__section-title">
							{ section.title }
						</h2>
					) : null }
					{ section.description ? (
						<p className="wpextrulepricing-settings-ui__section-desc">
							{ section.description }
						</p>
					) : null }
					<div className="wpextrulepricing-settings-ui__fields">
						{ section.fields.map( ( field ) => (
							<div
								key={ field.id }
								className="wpextrulepricing-settings-ui__field"
							>
								<SchemaField
									field={ field }
									value={ values[ field.id ] }
									onChange={ ( v ) =>
										onFieldChange( field.id, v )
									}
								/>
							</div>
						) ) }
					</div>
				</div>
			) ) }
		</div>
	);
}

/**
 * @param {object} props
 * @param {array} props.tabs
 * @param {object} props.values
 * @param {Function} props.setValues
 */
export default function SchemaSettings( { tabs, values, setValues } ) {
	const { tabSlug } = useParams();
	const [ saving, setSaving ] = useState( false );
	const [ notice, setNotice ] = useState( null );

	const activeTab = useMemo(
		() => ( tabs || [] ).find( ( t ) => t.slug === tabSlug ),
		[ tabs, tabSlug ]
	);

	const tabValues = useMemo(
		() =>
			values && tabSlug && values[ tabSlug ]
				? { ...values[ tabSlug ] }
				: {},
		[ values, tabSlug ]
	);

	const onFieldChange = useCallback(
		( fieldId, v ) => {
			if ( ! tabSlug ) {
				return;
			}
			setValues( ( prev ) => ( {
				...prev,
				[ tabSlug ]: {
					...( prev[ tabSlug ] || {} ),
					[ fieldId ]: v,
				},
			} ) );
		},
		[ setValues, tabSlug ]
	);

	const save = useCallback( () => {
		if ( ! tabSlug ) {
			return;
		}
		setSaving( true );
		setNotice( null );
		apiFetch( {
			path: SETTINGS_UI_PATH,
			method: 'POST',
			data: {
				tab: tabSlug,
				values: values[ tabSlug ] || {},
			},
		} )
			.then( ( res ) => {
				if ( res.values ) {
					setValues( res.values );
				}
				setNotice( {
					status: 'success',
					message: res.message || __( 'Saved.', 'wp-ext-rule-pricing' ),
				} );
			} )
			.catch( ( err ) => {
				setNotice( {
					status: 'error',
					message:
						err?.message ||
						__( 'Could not save settings.', 'wp-ext-rule-pricing' ),
				} );
			} )
			.finally( () => setSaving( false ) );
	}, [ tabSlug, values, setValues ] );

	useEffect( () => {
		setupApiFetch();
	}, [] );

	return (
		<>
			<TabPanel
				tab={ activeTab }
				values={ tabValues }
				onFieldChange={ onFieldChange }
			/>
			<div className="wpextrulepricing-settings-ui__footer">
				{ notice ? (
					<Notice status={ notice.status } isDismissible={ false }>
						{ notice.message }
					</Notice>
				) : null }
				<Button variant="primary" onClick={ save } disabled={ saving }>
					{ saving
						? __( 'Saving…', 'wp-ext-rule-pricing' )
						: __( 'Save settings', 'wp-ext-rule-pricing' ) }
				</Button>
			</div>
		</>
	);
}

export function useSettingsUiBootstrap() {
	const [ tabs, setTabs ] = useState( [] );
	const [ values, setValues ] = useState( {} );
	const [ loading, setLoading ] = useState( true );
	const [ error, setError ] = useState( null );

	useEffect( () => {
		setupApiFetch();
		let cancelled = false;
		setLoading( true );
		setError( null );
		apiFetch( { path: SETTINGS_UI_PATH } )
			.then( ( data ) => {
				if ( cancelled ) {
					return;
				}
				setTabs( Array.isArray( data.tabs ) ? data.tabs : [] );
				setValues(
					data.values && typeof data.values === 'object'
						? data.values
						: {}
				);
			} )
			.catch( ( err ) => {
				if ( ! cancelled ) {
					setError(
						err?.message ||
							__( 'Could not load settings UI.', 'wp-ext-rule-pricing' )
					);
				}
			} )
			.finally( () => {
				if ( ! cancelled ) {
					setLoading( false );
				}
			} );
		return () => {
			cancelled = true;
		};
	}, [] );

	return { tabs, values, setValues, loading, error };
}
