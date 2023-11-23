import { useEffect, useState } from 'react'
import { useForm } from '@inertiajs/react';
import Input from '../../../Components/Form/Input';
import TableRow from '../../../Components/Table/TableRow';
import TableHeaderCell from '../../../Components/Table/TableHeaderCell';
import TableDataCell from '../../../Components/Table/TableDataCell';;

// TODO: 親コンポーネントにモーダルを閉じる処理を渡す
export default function CustomerInfo() {
  const { data, setData, errors } = useForm({
    keyword: '',
  });

  const [customers, setCustomers] = useState([]);

  useEffect(() => {
    fetchCustomers();
  }, []);

  async function fetchCustomers() {
    const response = await fetch(`/api/customers?keyword=${data.keyword}`);
    const customersJson = await response.json();
    setCustomers(customersJson);
  }

  function submit(e) {
    e.preventDefault();
    fetchCustomers();
  }

  function handleChange(e) {
    setData('keyword', e.target.value);
  }

  function handleClickSelect(e) {
    const selectedCustomerId = e.currentTarget.dataset.customerId;

    console.log(selectedCustomerId + 'を選択'); // TEMP
    // TODO: 呼び出しもとにidを渡す処理追加
  }

  return (
    <>
      <form onSubmit={submit}>
        <header>
          <div className="u-flex u-mr-3">
            <Input
              type="search"
              onChange={handleChange}
            />
            <button className="btn btn-secondary">検索</button>
          </div>
          {errors.keyword && <div className="invalid-feedback">{errors.keyword}</div>}
        </header>
      </form>

      <div className="table-wrapper is-scrollable u-mt-4">
        <table className="table">
          <thead className="table-header">
            <TableRow>
              <TableHeaderCell className="u-w-80"></TableHeaderCell>
              <TableHeaderCell className="u-w-64">ID</TableHeaderCell>
              <TableHeaderCell>取引先名</TableHeaderCell>
              <TableHeaderCell>TEL</TableHeaderCell>
              <TableHeaderCell>住所</TableHeaderCell>
            </TableRow>
          </thead>
          <tbody className="table-body">
            {customers.map(customer => (
              <TableRow key={customer.id} className="is-hoverable">
                <TableDataCell>
                  <button
                    className="btn btn-secondary"
                    onClick={handleClickSelect}
                    data-customer-id={customer.id}
                  >
                    選択
                  </button>
                </TableDataCell>
                <TableDataCell>{customer.id}</TableDataCell>
                <TableDataCell>{customer.name}</TableDataCell>
                <TableDataCell>{customer.tel}</TableDataCell>
                <TableDataCell>{customer.address}</TableDataCell>
              </TableRow>
            ))}
          </tbody>
        </table>
      </div>
    </>
  );
}
