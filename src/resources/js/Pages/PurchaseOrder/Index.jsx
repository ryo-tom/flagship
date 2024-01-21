import { Link, useForm, usePage } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import Alert from '@/Components/Alert';
import Pagination from '@/Components/Pagination';
import KeywordSearchForm from '@/Components/KeywordSearchForm';
import PurchaseOrderTable from "./Partials/PurchaseOrderTable";


import CustomSelect from '@/Components/Form/CustomSelect';
import DateRangePicker from '@/Components/DateRangePicker';
import FormLabel from '@/Components/Form/FormLabel';
import Input from '@/Components/Form/Input';
import ToggleFilterButton from '@/Components/ToggleFilterButton';
import { formatCurrency } from '@/Utils/priceCalculator';

const Index = ({ purchaseOrders }) => {
  const urlParams = route().params;
  const { flash } = usePage().props;

  const { data, setData, get, errors } = useForm({
    keyword: urlParams.keyword || '',
    start_date: urlParams.start_date || '',
    end_date: urlParams.end_date || '',
  });

  function submit(e) {
    e.preventDefault();
    get(route('purchase-orders.index'), {
      preserveState: true,
    });
  };

  return (
    <>
      <h1 className="content-title">発注 一覧</h1>

      <div className="content-navbar">
        <Link
          href={route('purchase-orders.create')}
          className="btn btn-primary u-mr-3"
        >
          新規登録
        </Link>

        <KeywordSearchForm
          placeholder="発注ID, 仕入先名で検索"
          data={data}
          setData={setData}
          errors={errors}
          submit={submit}
        />

        <DateRangePicker
          dateColumnLabel="発注日"
          data={data}
          setData={setData}
          errors={errors}
          submit={submit}
        />

        <div className="record-count">
          {purchaseOrders.total}件
        </div>
        <Pagination paginator={purchaseOrders} />
      </div>

      <Alert type={flash.type} message={flash.message} />

      <PurchaseOrderTable purchaseOrders={purchaseOrders.data} />

    </>
  );
}

Index.layout = page => <AppLayout title="発注 一覧" children={page} />

export default Index
