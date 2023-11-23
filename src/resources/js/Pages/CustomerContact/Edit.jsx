import AppLayout from '@/Layouts/AppLayout';
import { Link, useForm, usePage } from "@inertiajs/react";
import CancelButton from '../../Components/CancelButton';
import TableInputRow from '../../Components/TableInputRow';
import TableGenericSelectRow from '../../Components/TableGenericSelectRow';
import TableTextAreaRow from '../../Components/TableTextAreaRow';
import TableRow from '../../Components/Table/TableRow';
import TableHeaderCell from '../../Components/Table/TableHeaderCell';
import TableDataCell from '../../Components/Table/TableDataCell';
import FormLabel from '../../Components/Form/FormLabel';
import RadioGroup from '../../Components/Form/RadioGroup';

const Edit = ({ contact, userSelectOptions, customerSelectOptions }) => {
  const { flash } = usePage().props;

  const { data, setData, patch, processing, errors, reset, isDirty } = useForm({
    customer_id: contact.customer_id,
    name: contact.name,
    name_kana: contact.name_kana || '',
    tel: contact.tel || '',
    mobile_number: contact.mobile_number || '',
    email: contact.email || '',
    position: contact.position || '',
    role: contact.role || '',
    is_active: contact.is_active === 1,
    note: contact.note || '',
    in_charge_user_id: contact.in_charge_user_id || '',
  });

  function submit(e) {
    e.preventDefault();
    patch(route('contacts.update', contact), {
      onSuccess: () => {
        reset();
      }
    });
  };

  return (
    <>
      <h1 className="content-title">連絡先 編集</h1>
      <div className="content-navbar">
        <button
          type="submit"
          form="customerContactUpdateForm"
          className="btn btn-primary u-mr-3"
          disabled={processing}
        >
          更新する
        </button>
        <CancelButton isDirty={isDirty} route={route('contacts.index')} />
        {processing && <span>Now Loading...</span>}
        <Link
          onBefore={() => confirm('本当に削除しますか？')}
          href={route('contacts.destroy', contact)}
          method="delete"
          className="btn btn-danger u-ml-auto"
          as="button"
        >
          削除
        </Link>
      </div>

      {flash.message && (
        <div className="alert alert-danger">{flash.message}</div>
      )}

      <form id="customerContactUpdateForm" onSubmit={submit}>
        <div className="table-wrapper">
          <table className="table">
            <tbody className="tbody">

              <TableGenericSelectRow
                label="所属取引先"
                name="customer_id"
                data={data}
                setData={setData}
                errors={errors}
                options={customerSelectOptions}
                isRequired={true}
              />

              <TableInputRow labelName="担当者名" inputName="name" data={data} errors={errors} setData={setData} isRequired={true} />
              <TableInputRow labelName="よみがな" inputName="name_kana" data={data} errors={errors} setData={setData} />
              <TableInputRow labelName="TEL" inputName="tel" data={data} errors={errors} setData={setData} />
              <TableInputRow labelName="携帯番号" inputName="mobile_number" data={data} errors={errors} setData={setData} />
              <TableInputRow labelName="E-mail" inputName="email" data={data} errors={errors} setData={setData} />
              <TableInputRow labelName="役職" inputName="position" data={data} errors={errors} setData={setData} />
              <TableInputRow labelName="役割" inputName="role" data={data} errors={errors} setData={setData} />

              <TableRow className="is-flexible">
                <TableHeaderCell>
                  <FormLabel htmlFor="is_active-true" label="使用状況" isRequired={true} />
                </TableHeaderCell>
                <TableDataCell className="u-flex">
                  <RadioGroup
                    id="is_active"
                    options={[
                      { value: true, label: '使用中' },
                      { value: false, label: '使用不可' },
                    ]}
                    value={data.is_active}
                    onChange={e => setData('is_active', e.target.value === 'true')}
                    error={errors.is_active}
                  />
                  {errors.is_active && (<div className="invalid-feedback">{errors.is_active}</div>)}
                </TableDataCell>
              </TableRow>

              <TableTextAreaRow
                labelName="備考"
                inputName="note"
                data={data}
                errors={errors}
                setData={setData}
                isRequired={false}
              />
              <TableGenericSelectRow
                label="担当ユーザー"
                name="in_charge_user_id"
                data={data}
                setData={setData}
                errors={errors}
                options={userSelectOptions}
                isRequired={false}
              />

            </tbody>
          </table>
        </div>
      </form>
    </>
  );
}

Edit.layout = page => <AppLayout children={page} />

export default Edit
