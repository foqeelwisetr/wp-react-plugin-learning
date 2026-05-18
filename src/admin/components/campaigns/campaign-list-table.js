/**
 * Autonami-style campaigns listing (checkboxes, filters, row actions).
 */
import { __ } from '@wordpress/i18n';
import { useMemo, useState } from '@wordpress/element';
import {
	Button,
	DropdownMenu,
	SearchControl,
	Spinner,
} from '@wordpress/components';
import { moreVertical } from '@wordpress/icons';
import { AtrcLink } from 'atrc';
import { useNavigate } from 'react-router-dom';

function statusLabel( status ) {
	switch ( status ) {
		case 'active':
			return __( 'Active', 'wp-ext-rule-pricing' );
		case 'paused':
			return __( 'Inactive', 'wp-ext-rule-pricing' );
		default:
			return __( 'Draft', 'wp-ext-rule-pricing' );
	}
}

function filterByStatus( list, filter ) {
	if ( filter === 'all' ) {
		return list;
	}
	if ( filter === 'active' ) {
		return list.filter( ( r ) => r.status === 'active' );
	}
	return list.filter( ( r ) => r.status !== 'active' );
}

export default function CampaignListTable( {
	campaigns,
	loading,
	onRefresh,
	onDelete,
	onDuplicate,
	onToggleStatus,
} ) {
	const navigate = useNavigate();
	const [ statusFilter, setStatusFilter ] = useState( 'all' );
	const [ search, setSearch ] = useState( '' );
	const [ selected, setSelected ] = useState( [] );

	const counts = useMemo( () => {
		const active = campaigns.filter( ( c ) => c.status === 'active' ).length;
		return {
			all: campaigns.length,
			active,
			inactive: campaigns.length - active,
		};
	}, [ campaigns ] );

	const filtered = useMemo( () => {
		let rows = filterByStatus( campaigns, statusFilter );
		const q = search.trim().toLowerCase();
		if ( q ) {
			rows = rows.filter(
				( r ) =>
					( r.title || '' ).toLowerCase().includes( q ) ||
					( r.event || '' ).toLowerCase().includes( q ) ||
					( r.category || '' ).toLowerCase().includes( q )
			);
		}
		return rows;
	}, [ campaigns, statusFilter, search ] );

	const allSelected =
		filtered.length > 0 && selected.length === filtered.length;
	const someSelected = selected.length > 0 && ! allSelected;

	const toggleAll = () => {
		if ( allSelected ) {
			setSelected( [] );
		} else {
			setSelected( filtered.map( ( r ) => r.id ) );
		}
	};

	const toggleOne = ( id ) => {
		setSelected( ( prev ) =>
			prev.includes( id ) ? prev.filter( ( x ) => x !== id ) : [ ...prev, id ]
		);
	};

	return (
		<div className="wpextrulepricing-campaign-list">
			<div className="wpextrulepricing-campaign-list__header">
				<h2 className="wpextrulepricing-campaign-list__title">
					{ __( 'Campaigns', 'wp-ext-rule-pricing' ) }
					<span className="wpextrulepricing-campaign-list__count">
						{ filtered.length }{ ' ' }
						{ __( 'Results', 'wp-ext-rule-pricing' ) }
					</span>
				</h2>
			</div>

			<div className="wpextrulepricing-campaign-list__filters">
				<div
					className="wpextrulepricing-campaign-list__status-tabs"
					role="tablist"
				>
					{ [
						{ key: 'all', label: __( 'All', 'wp-ext-rule-pricing' ) },
						{ key: 'active', label: __( 'Active', 'wp-ext-rule-pricing' ) },
						{
							key: 'inactive',
							label: __( 'Inactive', 'wp-ext-rule-pricing' ),
						},
					].map( ( tab ) => (
						<button
							key={ tab.key }
							type="button"
							role="tab"
							className={
								statusFilter === tab.key
									? 'wpextrulepricing-campaign-list__status-tab is-active'
									: 'wpextrulepricing-campaign-list__status-tab'
							}
							onClick={ () => setStatusFilter( tab.key ) }
						>
							{ tab.label } ({ counts[ tab.key ] })
						</button>
					) ) }
				</div>
				<div className="wpextrulepricing-campaign-list__filters-right">
					<SearchControl
						value={ search }
						onChange={ setSearch }
						placeholder={ __( 'Search…', 'wp-ext-rule-pricing' ) }
						__nextHasNoMarginBottom
					/>
					<Button variant="secondary" onClick={ onRefresh }>
						{ __( 'Refresh', 'wp-ext-rule-pricing' ) }
					</Button>
				</div>
			</div>

			{ loading ? (
				<p className="wpextrulepricing-campaign-list__loading">
					<Spinner /> { __( 'Loading campaigns…', 'wp-ext-rule-pricing' ) }
				</p>
			) : (
				<div className="wpextrulepricing-campaign-list__table-wrap">
					<table className="wpextrulepricing-campaign-list__table widefat">
						<thead>
							<tr>
								<td className="check-column">
									<input
										type="checkbox"
										checked={ allSelected }
										ref={ ( el ) => {
											if ( el ) {
												el.indeterminate = someSelected;
											}
										} }
										onChange={ toggleAll }
										aria-label={ __(
											'Select all',
											'wp-ext-rule-pricing'
										) }
									/>
								</td>
								<th>{ __( 'Name', 'wp-ext-rule-pricing' ) }</th>
								<th>{ __( 'Event', 'wp-ext-rule-pricing' ) }</th>
								<th>{ __( 'Category', 'wp-ext-rule-pricing' ) }</th>
								<th>{ __( 'Priority', 'wp-ext-rule-pricing' ) }</th>
								<th>{ __( 'Status', 'wp-ext-rule-pricing' ) }</th>
								<th className="wpextrulepricing-campaign-list__actions-col" />
							</tr>
						</thead>
						<tbody>
							{ filtered.length === 0 ? (
								<tr>
									<td colSpan={ 7 }>
										{ __( 'No campaigns match your filters.', 'wp-ext-rule-pricing' ) }
									</td>
								</tr>
							) : (
								filtered.map( ( row ) => (
									<tr
										key={ row.id }
										className={
											selected.includes( row.id )
												? 'is-selected'
												: ''
										}
									>
										<th scope="row" className="check-column">
											<input
												type="checkbox"
												checked={ selected.includes( row.id ) }
												onChange={ () => toggleOne( row.id ) }
												aria-label={ row.title }
											/>
										</th>
										<td>
											<AtrcLink
												type="router-link"
												to={ `/campaigns/${ row.id }` }
												className="wpextrulepricing-campaign-list__name"
											>
												<strong>{ row.title }</strong>
											</AtrcLink>
										</td>
										<td>{ row.event || '—' }</td>
										<td>{ row.category || '—' }</td>
										<td>{ row.priority }</td>
										<td>
											<span
												className={ `wpextrulepricing-campaign-list__status wpextrulepricing-campaign-list__status--${ row.status }` }
											>
												{ statusLabel( row.status ) }
											</span>
										</td>
										<td className="wpextrulepricing-campaign-list__actions-col">
											<DropdownMenu
												icon={ moreVertical }
												label={ __( 'Actions', 'wp-ext-rule-pricing' ) }
												controls={ [
													{
														title: __(
															'Edit',
															'wp-ext-rule-pricing'
														),
														onClick: () =>
															navigate(
																`/campaigns/${ row.id }`
															),
													},
													{
														title:
															row.status === 'active'
																? __(
																		'Deactivate',
																		'wp-ext-rule-pricing'
																  )
																: __(
																		'Activate',
																		'wp-ext-rule-pricing'
																  ),
														onClick: () =>
															onToggleStatus( row ),
													},
													{
														title: __(
															'Duplicate',
															'wp-ext-rule-pricing'
														),
														onClick: () =>
															onDuplicate( row ),
													},
													{
														title: __(
															'Delete',
															'wp-ext-rule-pricing'
														),
														onClick: () =>
															onDelete( row ),
													},
												] }
											/>
										</td>
									</tr>
								) )
							) }
						</tbody>
					</table>
				</div>
			) }
		</div>
	);
}
