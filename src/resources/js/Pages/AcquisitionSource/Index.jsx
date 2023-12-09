import AppLayout from '@/Layouts/AppLayout';
import { usePage } from '@inertiajs/react';
import AcquisitionSourceCreateForm from "./Partials/AcquisitionSourceCreateForm";
import AcquisitionSourceTable from './Partials/AcquisitionSourceTable';

const Index = ({ acquisitionSources }) => {
  const { flash } = usePage().props;

  return (
    <>
      <h1 className="content-title">顧客獲得元 管理</h1>

      <AcquisitionSourceCreateForm />

      {flash.message && (
        <div className="alert alert-success">{flash.message}</div>
      )}

      <AcquisitionSourceTable acquisitionSources={acquisitionSources} />

    </>
  );
}

Index.layout = page => <AppLayout children={page} />

export default Index
