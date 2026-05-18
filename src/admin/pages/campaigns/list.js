/**
 * Campaign listing — FunnelKit-style table with bulk actions and search.
 */
import { __ } from '@wordpress/i18n';
import { useEffect, useState } from '@wordpress/element';
import { Button } from '@wordpress/components';
import { useNavigate } from 'react-router-dom';
import CampaignShell from '../../components/campaigns/campaign-shell';
import CampaignListTable from '../../components/campaigns/campaign-list-table';
import DeleteCampaignModal from '../../components/campaigns/delete-campaign-modal';
import ProFeatureModal from '../../components/campaigns/pro-feature-modal';
import { useProUpsell } from '../../hooks/use-pro-upsell';
import {
	createCampaign,
	deleteCampaign,
	fetchCampaigns,
	saveCampaign,
} from '../../hooks/use-campaigns-api';

export default function CampaignsList() {
	const navigate = useNavigate();
	const {
		openUpsell,
		closeUpsell,
		activeUpsell,
	} = useProUpsell();

	const [ campaigns, setCampaigns ] = useState( [] );
	const [ loading, setLoading ] = useState( true );
	const [ error, setError ] = useState( null );
	const [ creating, setCreating ] = useState( false );
	const [ deleteTarget, setDeleteTarget ] = useState( null );
	const [ bulkDeleteIds, setBulkDeleteIds ] = useState( null );
	const [ deleting, setDeleting ] = useState( false );

	const load = () => {
		setLoading( true );
		setError( null );
		fetchCampaigns()
			.then( ( list ) => setCampaigns( Array.isArray( list ) ? list : [] ) )
			.catch( ( err ) =>
				setError(
					err?.message ||
						__( 'Could not load campaigns.', 'wp-ext-rule-pricing' )
				)
			)
			.finally( () => setLoading( false ) );
	};

	useEffect( () => {
		load();
	}, [] );

	const handleAdd = async () => {
		setCreating( true );
		try {
			const created = await createCampaign( {
				title: __( 'New campaign', 'wp-ext-rule-pricing' ),
			} );
			if ( created?.id ) {
				navigate( `/campaigns/${ created.id }/schedule` );
			} else {
				load();
			}
		} catch ( err ) {
			setError(
				err?.message ||
					__( 'Could not create campaign.', 'wp-ext-rule-pricing' )
			);
		} finally {
			setCreating( false );
		}
	};

	const handleDeleteRequest = ( row ) => {
		setBulkDeleteIds( null );
		setDeleteTarget( row );
	};

	const handleBulkDeleteRequest = ( ids ) => {
		setDeleteTarget( null );
		setBulkDeleteIds( ids );
	};

	const handleDeleteCancel = () => {
		if ( ! deleting ) {
			setDeleteTarget( null );
			setBulkDeleteIds( null );
		}
	};

	const handleDeleteConfirm = async () => {
		setDeleting( true );
		setError( null );
		try {
			if ( bulkDeleteIds?.length ) {
				await Promise.all(
					bulkDeleteIds.map( ( id ) => deleteCampaign( id ) )
				);
				setBulkDeleteIds( null );
			} else if ( deleteTarget?.id ) {
				await deleteCampaign( deleteTarget.id );
				setDeleteTarget( null );
			}
			load();
		} catch ( err ) {
			setError(
				err?.message ||
					__( 'Could not delete campaign.', 'wp-ext-rule-pricing' )
			);
		} finally {
			setDeleting( false );
		}
	};

	const handleDuplicate = async ( row ) => {
		try {
			const { id: _id, created_at, updated_at, ...rest } = row;
			await createCampaign( {
				...rest,
				title: `${ row.title } (${ __( 'Copy', 'wp-ext-rule-pricing' ) })`,
				status: 'draft',
			} );
			load();
		} catch ( err ) {
			setError(
				err?.message ||
					__( 'Could not duplicate campaign.', 'wp-ext-rule-pricing' )
			);
		}
	};

	const handleToggleStatus = async ( row ) => {
		const next = row.status === 'active' ? 'paused' : 'active';
		try {
			await saveCampaign( row.id, { ...row, status: next } );
			load();
		} catch ( err ) {
			setError(
				err?.message ||
					__( 'Could not update status.', 'wp-ext-rule-pricing' )
			);
		}
	};

	const deleteModalOpen = !! deleteTarget || !! bulkDeleteIds?.length;

	return (
		<>
			<CampaignShell
				actions={
					<Button
						variant="primary"
						onClick={ handleAdd }
						isBusy={ creating }
						disabled={ creating }
					>
						{ __( 'Create Campaign', 'wp-ext-rule-pricing' ) }
					</Button>
				}
			>
				{ error && (
					<p className="wpextrulepricing-campaigns__error">{ error }</p>
				) }
				<CampaignListTable
					campaigns={ campaigns }
					loading={ loading }
					onRefresh={ load }
					onDelete={ handleDeleteRequest }
					onBulkDelete={ handleBulkDeleteRequest }
					onDuplicate={ handleDuplicate }
					onToggleStatus={ handleToggleStatus }
					onProAction={ openUpsell }
				/>
			</CampaignShell>
			<DeleteCampaignModal
				campaign={ deleteTarget }
				bulkCount={ bulkDeleteIds?.length || 0 }
				isOpen={ deleteModalOpen }
				isDeleting={ deleting }
				onCancel={ handleDeleteCancel }
				onConfirm={ handleDeleteConfirm }
			/>
			<ProFeatureModal
				upsell={ activeUpsell }
				isOpen={ !! activeUpsell }
				onClose={ closeUpsell }
			/>
		</>
	);
}
