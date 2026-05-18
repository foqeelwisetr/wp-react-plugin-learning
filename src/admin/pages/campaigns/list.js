/**
 * Campaign listing — Autonami-style table with checkboxes.
 */
import { __ } from '@wordpress/i18n';
import { useEffect, useState } from '@wordpress/element';
import { Button } from '@wordpress/components';
import { useNavigate } from 'react-router-dom';
import CampaignShell from '../../components/campaigns/campaign-shell';
import CampaignListTable from '../../components/campaigns/campaign-list-table';
import DeleteCampaignModal from '../../components/campaigns/delete-campaign-modal';
import {
	createCampaign,
	deleteCampaign,
	fetchCampaigns,
	saveCampaign,
} from '../../hooks/use-campaigns-api';

export default function CampaignsList() {
	const navigate = useNavigate();
	const [ campaigns, setCampaigns ] = useState( [] );
	const [ loading, setLoading ] = useState( true );
	const [ error, setError ] = useState( null );
	const [ creating, setCreating ] = useState( false );
	const [ deleteTarget, setDeleteTarget ] = useState( null );
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
				navigate( `/campaigns/${ created.id }` );
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
		setDeleteTarget( row );
	};

	const handleDeleteCancel = () => {
		if ( ! deleting ) {
			setDeleteTarget( null );
		}
	};

	const handleDeleteConfirm = async () => {
		if ( ! deleteTarget?.id ) {
			return;
		}
		setDeleting( true );
		setError( null );
		try {
			await deleteCampaign( deleteTarget.id );
			setDeleteTarget( null );
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
		const next =
			row.status === 'active' ? 'paused' : 'active';
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

	return (
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
				onDuplicate={ handleDuplicate }
				onToggleStatus={ handleToggleStatus }
			/>
			<DeleteCampaignModal
				campaign={ deleteTarget }
				isOpen={ !! deleteTarget }
				isDeleting={ deleting }
				onCancel={ handleDeleteCancel }
				onConfirm={ handleDeleteConfirm }
			/>
		</CampaignShell>
	);
}
