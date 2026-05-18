/**
 * FunnelKit / Autonami-style campaigns listing — search, filters, bulk actions.
 */
import { __, sprintf } from '@wordpress/i18n';
import { useEffect, useMemo, useState } from '@wordpress/element';
import {
	Button,
	DropdownMenu,
	SearchControl,
	SelectControl,
	Spinner,
} from '@wordpress/components';
import { moreVertical, update } from '@wordpress/icons';
import { AtrcLink } from 'atrc';
import { useNavigate } from 'react-router-dom';
import { getCampaignListConfig } from '../../utils/campaign-list-config';
import { isPro } from '../../utils/is-pro';

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

function mergeCategories( phpCategories, campaigns ) {
	const map = new Map();
	( phpCategories || [] ).forEach( ( c ) => {
		if ( c?.value !== undefined ) {
			map.set( c.value, c.label );
		}
	} );
	campaigns.forEach( ( row ) => {
		if ( row.category ) {
			map.set( row.category, row.category );
		}
	} );
	const options = Array.from( map.entries() ).map( ( [ value, label ] ) => ( {
		value,
		label,
	} ) );
	if ( ! options.some( ( o ) => o.value === '' ) ) {
		options.unshift( {
			value: '',
			label: __( 'All Categories', 'wp-ext-rule-pricing' ),
		} );
	}
	return options;
}

export default function CampaignListTable( {
	campaigns,
	loading,
	onRefresh,
	onDelete,
	onBulkDelete,
	onDuplicate,
	onToggleStatus,
	onProAction,
} ) {
	const navigate = useNavigate();
	const listConfig = getCampaignListConfig();
	const bulkActions = listConfig.bulk_actions || [];
	const toolbarActions = listConfig.toolbar_actions || [];

	const [ statusFilter, setStatusFilter ] = useState( 'all' );
	const [ search, setSearch ] = useState( '' );
	const [ categoryFilter, setCategoryFilter ] = useState( '' );
	const [ selected, setSelected ] = useState( [] );

	const categoryOptions = useMemo(
		() => mergeCategories( listConfig.categories, campaigns ),
		[ listConfig.categories, campaigns ]
	);

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
		if ( categoryFilter ) {
			rows = rows.filter( ( r ) => ( r.category || '' ) === categoryFilter );
		}
		const q = search.trim().toLowerCase();
		if ( q ) {
			rows = rows.filter(
				( r ) =>
					( r.title || '' ).toLowerCase().includes( q ) ||
					( r.event || '' ).toLowerCase().includes( q ) ||
					( r.category || '' ).toLowerCase().includes( q ) ||
					String( r.priority ?? '' ).includes( q )
			);
		}
		return rows;
	}, [ campaigns, statusFilter, search, categoryFilter ] );

	const selectedCount = selected.length;
	const allSelected =
		filtered.length > 0 && selected.length === filtered.length;
	const someSelected = selected.length > 0 && ! allSelected;

	useEffect( () => {
		setSelected( [] );
	}, [ statusFilter, search, categoryFilter ] );

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

	const clearSelection = () => setSelected( [] );

	const handleBulkAction = ( action ) => {
		if ( action.pro && ! isPro() ) {
			onProAction( action.upsell_id || 'default' );
			return;
		}
		if ( action.id === 'delete' && onBulkDelete ) {
			onBulkDelete( selected );
		}
	};

	const handleToolbarAction = ( action ) => {
		if ( action.pro && ! isPro() ) {
			onProAction( action.upsell_id || 'default' );
		}
	};

	const searchPlaceholder =
		listConfig.search_placeholder ||
		__( 'Search…', 'wp-ext-rule-pricing' );

	const rowActions = ( row ) => [
		{
			title: __( 'Edit', 'wp-ext-rule-pricing' ),
			onClick: () => navigate( `/campaigns/${ row.id }/schedule` ),
		},
		{
			title:
				row.status === 'active'
					? __( 'Deactivate', 'wp-ext-rule-pricing' )
					: __( 'Activate', 'wp-ext-rule-pricing' ),
			onClick: () => onToggleStatus( row ),
		},
		{
			title: __( 'Duplicate', 'wp-ext-rule-pricing' ),
			onClick: () => onDuplicate( row ),
		},
		{
			title: __( 'Delete', 'wp-ext-rule-pricing' ),
			onClick: () => onDelete( row ),
		},
	];

	return (
		<div className="wpextrulepricing-campaign-list">
			<div className="wpextrulepricing-campaign-list__header">
				<h2 className="wpextrulepricing-campaign-list__title">
					{ __( 'Campaigns', 'wp-ext-rule-pricing' ) }
					<span className="wpextrulepricing-campaign-list__count">
						({ filtered.length }{ ' ' }
						{ __( 'Results', 'wp-ext-rule-pricing' ) })
					</span>
				</h2>
			</div>

			<div className="wpextrulepricing-campaign-list__toolbar">
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

				<div className="wpextrulepricing-campaign-list__toolbar-right">
					<SearchControl
						value={ search }
						onChange={ setSearch }
						placeholder={ searchPlaceholder }
						__nextHasNoMarginBottom
					/>
					{ categoryOptions.length > 1 ? (
						<SelectControl
							value={ categoryFilter }
							options={ categoryOptions }
							onChange={ setCategoryFilter }
							hideLabelFromVision
							__nextHasNoMarginBottom
						/>
					) : null }
					<Button
						variant="secondary"
						icon={ update }
						onClick={ onRefresh }
						label={ __( 'Refresh', 'wp-ext-rule-pricing' ) }
					>
						{ __( 'Refresh', 'wp-ext-rule-pricing' ) }
					</Button>
					{ toolbarActions.map( ( action ) => (
						<Button
							key={ action.id }
							variant="secondary"
							onClick={ () => handleToolbarAction( action ) }
						>
							{ action.label }
							{ action.pro && ! isPro() ? (
								<span
									className="wpextrulepricing-pro-crown"
									aria-hidden
								>
									👑
								</span>
							) : null }
						</Button>
					) ) }
				</div>
			</div>

			{ loading ? (
				<p className="wpextrulepricing-campaign-list__loading">
					<Spinner /> { __( 'Loading campaigns…', 'wp-ext-rule-pricing' ) }
				</p>
			) : (
				<div className="wpextrulepricing-campaign-list__table-wrap">
					{ selectedCount > 0 ? (
						<div className="wpextrulepricing-campaign-list__bulk-bar">
							<div className="wpextrulepricing-campaign-list__bulk-left">
								<input
									type="checkbox"
									className="wpextrulepricing-campaign-list__bulk-check"
									checked={ allSelected }
									ref={ ( el ) => {
										if ( el ) {
											el.indeterminate = someSelected;
										}
									} }
									onChange={ toggleAll }
									aria-label={ __( 'Select all', 'wp-ext-rule-pricing' ) }
								/>
								<span className="wpextrulepricing-campaign-list__bulk-count">
									{ sprintf(
										/* translators: %d: number of selected campaigns */
										__(
											'%d items selected in the list',
											'wp-ext-rule-pricing'
										),
										selectedCount
									) }
								</span>
								<Button
									variant="link"
									onClick={ clearSelection }
									className="wpextrulepricing-campaign-list__bulk-clear"
								>
									{ __( 'Clear All', 'wp-ext-rule-pricing' ) }
								</Button>
							</div>
							<div className="wpextrulepricing-campaign-list__bulk-actions">
								{ bulkActions.map( ( action ) => (
									<Button
										key={ action.id }
										variant="secondary"
										className={
											action.id === 'delete'
												? 'is-destructive'
												: ''
										}
										onClick={ () => handleBulkAction( action ) }
									>
										{ action.id === 'delete' ? (
											<span aria-hidden>🗑</span>
										) : null }
										{ action.label }
										{ action.pro && ! isPro() ? (
											<span
												className="wpextrulepricing-pro-crown"
												aria-hidden
											>
												👑
											</span>
										) : null }
									</Button>
								) ) }
							</div>
						</div>
					) : null }

					<table className="wpextrulepricing-campaign-list__table widefat">
						<thead>
							<tr>
								<th className="wpextrulepricing-campaign-list__col-select">
									{ selectedCount === 0 ? (
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
									) : null }
								</th>
								<th>{ __( 'Name', 'wp-ext-rule-pricing' ) }</th>
								<th>{ __( 'Event', 'wp-ext-rule-pricing' ) }</th>
								<th>{ __( 'Category', 'wp-ext-rule-pricing' ) }</th>
								<th>{ __( 'Priority', 'wp-ext-rule-pricing' ) }</th>
								<th>{ __( 'Status', 'wp-ext-rule-pricing' ) }</th>
							</tr>
						</thead>
						<tbody>
							{ filtered.length === 0 ? (
								<tr>
									<td colSpan={ 6 }>
										{ __(
											'No campaigns match your filters.',
											'wp-ext-rule-pricing'
										) }
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
										<td className="wpextrulepricing-campaign-list__col-select">
											<div className="wpextrulepricing-campaign-list__row-select">
												<input
													type="checkbox"
													checked={ selected.includes( row.id ) }
													onChange={ () => toggleOne( row.id ) }
													aria-label={ row.title }
												/>
												<DropdownMenu
													icon={ moreVertical }
													label={ __(
														'Actions',
														'wp-ext-rule-pricing'
													) }
													controls={ rowActions( row ) }
												/>
											</div>
										</td>
										<td>
											<AtrcLink
												type="router-link"
												to={ `/campaigns/${ row.id }/schedule` }
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
