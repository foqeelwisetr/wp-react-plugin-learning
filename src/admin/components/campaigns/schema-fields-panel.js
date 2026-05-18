/**
 * PHP schema fields (depends_on + pro upsell on click).
 */
import { __ } from '@wordpress/i18n';
import {
	BaseControl,
	Button,
	CheckboxControl,
	RadioControl,
	SelectControl,
	TextControl,
	TextareaControl,
	ToggleControl,
} from '@wordpress/components';
import classNames from 'classnames';
import { isFieldVisible } from '../../utils/field-visibility';
import {
	getFieldColClass,
	getRowLabel,
	groupFieldsIntoRows,
} from '../../utils/field-row-groups';
import {
	getResolvedFieldValue,
	resolveTabFieldValues,
} from '../../utils/resolve-field-values';
import { isPro } from '../../utils/is-pro';

function ShowHideControl( { field, value, onChange, hideLabel } ) {
	const current = getResolvedFieldValue( field, { [ field.id ]: value } );
	return (
		<div className="wpextrulepricing-show-hide">
			{ [
				{ value: 'show', label: __( 'Show', 'wp-ext-rule-pricing' ) },
				{ value: 'hide', label: __( 'Hide', 'wp-ext-rule-pricing' ) },
			].map( ( opt ) => (
				<Button
					key={ opt.value }
					variant={ current === opt.value ? 'primary' : 'secondary' }
					onClick={ () => onChange( opt.value ) }
				>
					{ opt.label }
				</Button>
			) ) }
		</div>
	);
}

function YesNoControl( { field, value, onChange } ) {
	const current = getResolvedFieldValue( field, { [ field.id ]: value } ) || 'no';
	return (
		<div className="wpextrulepricing-yes-no">
			{ [
				{ value: 'yes', label: __( 'Yes', 'wp-ext-rule-pricing' ) },
				{ value: 'no', label: __( 'No', 'wp-ext-rule-pricing' ) },
			].map( ( opt ) => (
				<Button
					key={ opt.value }
					variant={ current === opt.value ? 'primary' : 'secondary' }
					onClick={ () => onChange( opt.value ) }
				>
					{ opt.label }
				</Button>
			) ) }
		</div>
	);
}

function ScheduleTypeControl( { field, value, onChange, onProClick } ) {
	const current = value || 'one_time';

	return (
		<div className="wpextrulepricing-schedule-type">
			{ ( field.options || [] ).map( ( opt ) => {
				const locked = !! opt.pro && ! isPro();
				const isActive = current === opt.value;
				return (
					<Button
						key={ opt.value }
						variant={ isActive && ! locked ? 'primary' : 'secondary' }
						className={ locked ? 'is-pro-option' : '' }
						onClick={ () => {
							if ( locked ) {
								onProClick( opt.upsell_id || 'default' );
								return;
							}
							onChange( opt.value );
						} }
					>
						{ opt.label }
						{ locked ? (
							<span className="wpextrulepricing-pro-badge">Pro</span>
						) : null }
					</Button>
				);
			} ) }
		</div>
	);
}

function SchemaField( { field, value, onChange, hideLabel, onProClick } ) {
	const commonNext = {
		__next40pxDefaultSize: true,
		__nextHasNoMarginBottom: true,
	};
	const resolvedValue = getResolvedFieldValue( field, { [ field.id ]: value } );
	const label = hideLabel ? undefined : field.label;
	const help = field.description;

	if ( field.pro && ! isPro() && field.type !== 'schedule_type' && field.type !== 'pro_modal' ) {
		return (
			<div className="wpextrulepricing-campaign-field is-pro-locked">
				<Button
					variant="secondary"
					onClick={ () => onProClick( field.upsell_id || 'default' ) }
				>
					{ __( 'Unlock in Pro', 'wp-ext-rule-pricing' ) }
					<span className="wpextrulepricing-pro-badge">Pro</span>
				</Button>
			</div>
		);
	}

	switch ( field.type ) {
		case 'help':
			return (
				<p className="wpextrulepricing-campaign-field__help description">
					<span className="dashicons dashicons-editor-help" aria-hidden />
					{ field.description }
					{ field.help_url ? (
						<>
							{ ' ' }
							<a href={ field.help_url } target="_blank" rel="noreferrer">
								{ __( 'Read Docs', 'wp-ext-rule-pricing' ) }
							</a>
						</>
					) : null }
				</p>
			);
		case 'pro_modal':
			return (
				<>
					{ help ? <p className="description">{ help }</p> : null }
					<Button
						variant="primary"
						onClick={ () => {
							if ( ! isPro() ) {
								onProClick( field.upsell_id || 'campaign_coupons' );
								return;
							}
							onChange( value ?? true );
						} }
					>
						{ field.button_text || __( 'Configure', 'wp-ext-rule-pricing' ) }
						{ ! isPro() ? (
							<span className="wpextrulepricing-pro-badge">Pro</span>
						) : null }
					</Button>
				</>
			);
		case 'show_hide':
			return (
				<BaseControl label={ label } help={ help }>
					<ShowHideControl
						field={ field }
						value={ value }
						onChange={ onChange }
					/>
				</BaseControl>
			);
		case 'yes_no':
			return (
				<BaseControl label={ label } help={ help }>
					<YesNoControl field={ field } value={ value } onChange={ onChange } />
				</BaseControl>
			);
		case 'schedule_type':
			return (
				<BaseControl label={ label } help={ help }>
					<ScheduleTypeControl
						field={ field }
						value={ value }
						onChange={ onChange }
						onProClick={ onProClick }
					/>
				</BaseControl>
			);
		case 'textarea':
			return (
				<TextareaControl
					label={ label }
					help={ help }
					value={ value ?? '' }
					rows={ field.rows || 4 }
					onChange={ onChange }
					{ ...commonNext }
				/>
			);
		case 'url':
			return (
				<TextControl
					type="url"
					label={ label }
					help={ help }
					placeholder={ field.placeholder || 'https://' }
					value={ value ?? '' }
					onChange={ onChange }
					{ ...commonNext }
				/>
			);
		case 'number':
			return (
				<TextControl
					type="number"
					label={ label }
					help={ help }
					value={ String( resolvedValue ?? '' ) }
					onChange={ ( v ) => onChange( v === '' ? '' : Number( v ) ) }
					{ ...commonNext }
				/>
			);
		case 'date':
			return (
				<TextControl
					type="date"
					label={ label }
					help={ help }
					value={ resolvedValue ?? '' }
					min={ field.min || undefined }
					max={ field.max || undefined }
					onChange={ onChange }
					{ ...commonNext }
				/>
			);
		case 'time':
			return (
				<TextControl
					type="time"
					label={ label }
					help={ help }
					value={ resolvedValue ?? '' }
					min={ field.min || undefined }
					max={ field.max || undefined }
					step={ field.step || undefined }
					onChange={ onChange }
					{ ...commonNext }
				/>
			);
		case 'toggle':
			return (
				<ToggleControl
					label={ label }
					help={ help }
					checked={ !! value }
					onChange={ onChange }
				/>
			);
		case 'checkbox':
			return (
				<CheckboxControl
					label={ label }
					help={ help }
					checked={ !! value }
					onChange={ onChange }
				/>
			);
		case 'select':
			return (
				<SelectControl
					label={ label }
					help={ help }
					value={ resolvedValue ?? '' }
					options={ ( field.options || [] ).map( ( o ) => ( {
						label:
							o.pro && ! isPro() ? `${ o.label } (Pro)` : o.label,
						value: o.value,
						disabled: !! o.pro && ! isPro(),
					} ) ) }
					onChange={ onChange }
					{ ...commonNext }
				/>
			);
		case 'radio':
			return (
				<BaseControl label={ label } help={ help }>
					<RadioControl
						selected={ resolvedValue ?? '' }
						options={ ( field.options || [] ).map( ( o ) => ( {
							label: o.pro && ! isPro() ? `${ o.label } (Pro)` : o.label,
							value: o.value,
							disabled: !! o.pro && ! isPro(),
						} ) ) }
						onChange={ onChange }
					/>
				</BaseControl>
			);
		case 'color':
			return (
				<BaseControl label={ label } help={ help }>
					<div className="wpextrulepricing-campaign-field__color">
						<input
							type="color"
							value={
								value && /^#[0-9A-Fa-f]{6}$/i.test( value )
									? value
									: '#333333'
							}
							onChange={ ( e ) => onChange( e.target.value ) }
						/>
						<span className="description">
							{ __( 'Select Color', 'wp-ext-rule-pricing' ) }
						</span>
						<TextControl
							value={ value ?? '' }
							onChange={ onChange }
							hideLabelFromVision
							{ ...commonNext }
						/>
					</div>
				</BaseControl>
			);
		case 'text':
		default:
			return (
				<TextControl
					label={ label }
					help={ help }
					placeholder={ field.placeholder }
					value={ value ?? '' }
					onChange={ onChange }
					{ ...commonNext }
				/>
			);
	}
}

export default function SchemaFieldsPanel( {
	tabId,
	sections,
	values,
	getFieldValue,
	onChange,
	onProClick,
} ) {
	if ( ! sections?.length ) {
		return (
			<p className="description">
				{ __( 'No fields registered for this tab.', 'wp-ext-rule-pricing' ) }
			</p>
		);
	}

	const tabValues = resolveTabFieldValues( sections, values || {} );
	const readValue = ( field ) => {
		if ( getFieldValue ) {
			const custom = getFieldValue( field );
			if (
				custom !== null &&
				custom !== undefined &&
				custom !== ''
			) {
				return custom;
			}
		}
		return getResolvedFieldValue( field, tabValues );
	};
	const handlePro = ( id ) => {
		if ( onProClick ) {
			onProClick( id );
		}
	};

	return (
		<div className="wpextrulepricing-campaign-fields">
			{ sections.map( ( section ) => (
				<div
					key={ section.id || section.title }
					className="wpextrulepricing-campaign-fields__section"
				>
					{ section.title ? (
						<h3 className="wpextrulepricing-campaign-fields__section-title">
							{ section.title }
						</h3>
					) : null }
					{ section.description ? (
						<p className="description wpextrulepricing-campaign-fields__section-desc">
							{ section.description }
						</p>
					) : null }
					<table className="wpextrulepricing-campaign-fields__table form-table">
						<tbody>
							{ groupFieldsIntoRows(
								section.fields || [],
								tabValues
							).map( ( group ) => {
								if ( group.type === 'row' ) {
									const rowKey = group.fields
										.map( ( f ) => f.id )
										.join( '-' );
									const rowLabel = getRowLabel( group.fields );
									const labelField = group.fields.find(
										( f ) => f.label
									);
									return (
										<tr
											key={ rowKey }
											className={ classNames(
												'wpextrulepricing-campaign-fields__row',
												'wpextrulepricing-campaign-fields__row--grid',
												group.rowClass
											) }
										>
											<th scope="row">
												{ rowLabel ? (
													<label
														htmlFor={
															labelField
																? `camp-${ tabId }-${ labelField.id }`
																: undefined
														}
													>
														{ rowLabel }
														{ group.fields.some(
															( f ) => f.pro
														) ? (
															<span className="wpextrulepricing-pro-badge">
																Pro
															</span>
														) : null }
													</label>
												) : null }
											</th>
											<td>
												<div
													className={ classNames(
														'wpextrulepricing-campaign-fields__grid',
														`wpextrulepricing-campaign-fields__grid--${ group.fields.length }`
													) }
												>
													{ group.fields.map(
														( field ) => (
															<div
																key={ field.id }
																className={ classNames(
																	'wpextrulepricing-campaign-fields__col',
																	getFieldColClass(
																		field
																	),
																	field.class
																) }
															>
																<SchemaField
																	field={ field }
																	value={ readValue(
																		field
																	) }
																	onChange={ (
																		v
																	) =>
																		onChange(
																			field.id,
																			v,
																			field
																		)
																	}
																	hideLabel
																	onProClick={
																		handlePro
																	}
																/>
															</div>
														)
													) }
												</div>
											</td>
										</tr>
									);
								}

								const field = group.fields[ 0 ];

								if (
									field.type === 'help' &&
									field.store === false
								) {
									return (
										<tr
											key={ field.id }
											className="wpextrulepricing-campaign-fields__row wpextrulepricing-campaign-fields__row--help"
										>
											<td colSpan={ 2 }>
												<SchemaField
													field={ field }
													value=""
													onChange={ () => {} }
													hideLabel
													onProClick={ handlePro }
												/>
											</td>
										</tr>
									);
								}

								const hideLabel = ! field.label;
								if ( hideLabel ) {
									return (
										<tr
											key={ field.id }
											className="wpextrulepricing-campaign-fields__row wpextrulepricing-campaign-fields__row--sub"
										>
											<td colSpan={ 2 }>
												<SchemaField
													field={ field }
													value={ readValue( field ) }
													onChange={ ( v ) =>
														onChange(
															field.id,
															v,
															field
														)
													}
													hideLabel
													onProClick={ handlePro }
												/>
											</td>
										</tr>
									);
								}

								return (
									<tr
										key={ field.id }
										className={ classNames(
											'wpextrulepricing-campaign-fields__row',
											field.row_class,
											field.class
										) }
									>
										<th scope="row">
											<label
												htmlFor={ `camp-${ tabId }-${ field.id }` }
											>
												{ field.label }
												{ field.pro ? (
													<span className="wpextrulepricing-pro-badge">
														Pro
													</span>
												) : null }
											</label>
										</th>
										<td>
											<div
												className={ classNames(
													'wpextrulepricing-campaign-fields__col',
													getFieldColClass( field ),
													field.class
												) }
											>
												<SchemaField
													field={ field }
													value={ readValue( field ) }
													onChange={ ( v ) =>
														onChange(
															field.id,
															v,
															field
														)
													}
													hideLabel
													onProClick={ handlePro }
												/>
											</div>
										</td>
									</tr>
								);
							} ) }
						</tbody>
					</table>
				</div>
			) ) }
		</div>
	);
}
