import { useForm } from '@inertiajs/react';
import TableInputRow from '../../../Components/TableInputRow';
import TableGenericSelectRow from '../../../Components/TableGenericSelectRow';
import TableTextAreaRow from '../../../Components/TableTextAreaRow';
import TableRow from '../../../Components/Table/TableRow';
import TableHeaderCell from '../../../Components/Table/TableHeaderCell';
import TableDataCell from '../../../Components/Table/TableDataCell';
import RadioGroup from '../../../Components/Form/RadioGroup';
import FormLabel from '../../../Components/Form/FormLabel';

export default function ContactForm({ customer, closeModal, userSelectOptions }) {
  const { data, setData, post, processing, errors, reset } = useForm({
    name: '',
    name_kana: '',
    tel: '',
    mobile_number: '',
    email: '',
    position: '',
    role: '',
    is_active: true,
    note: '',
    in_charge_user_id: '',
  });

  function submit(e) {
    e.preventDefault();
    post(route('customers.contacts.add', customer), {
      onSuccess: () => {
        reset();
        closeModal();
      }
    });
  };

  return (
    <>
      <form id="customerContactCreateForm" onSubmit={submit}>
        <div className="table-wrapper">
          <table className="table">
            <tbody className="tbody">
              <TableInputRow
                labelName="担当者名"
                inputName="name"
                data={data}
                errors={errors}
                setData={setData}
                isRequired={true}
              />

              <TableInputRow
                labelName="よみがな"
                inputName="name_kana"
                data={data}
                errors={errors}
                setData={setData}
              />

              <TableInputRow
                labelName="TEL"
                inputName="tel"
                data={data}
                errors={errors}
                setData={setData}
              />

              <TableInputRow
                labelName="携帯番号"
                inputName="mobile_number"
                data={data}
                errors={errors}
                setData={setData}
              />

              <TableInputRow
                labelName="E-mail"
                inputName="email"
                data={data}
                errors={errors}
                setData={setData}
              />

              <TableInputRow
                labelName="役職"
                inputName="position"
                data={data}
                errors={errors}
                setData={setData}
              />

              <TableInputRow
                labelName="役割"
                inputName="role"
                data={data}
                errors={errors}
                setData={setData}
              />

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
      <button
        type="submit"
        form="customerContactCreateForm"
        className="btn btn-primary u-mt-3"
        disabled={processing}
      >
        登録する
      </button>
    </>
  );
}

