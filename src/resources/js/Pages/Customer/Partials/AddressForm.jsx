import { useForm } from "@inertiajs/react";
import TableInputRow from '../../../Components/TableInputRow';
import TableTextAreaRow from '../../../Components/TableTextAreaRow';
import TableRow from '../../../Components/Table/TableRow';
import TableHeaderCell from '../../../Components/Table/TableHeaderCell';
import TableDataCell from '../../../Components/Table/TableDataCell';
import RadioGroup from '../../../Components/Form/RadioGroup';
import FormLabel from '../../../Components/Form/FormLabel';

export default function AddressForm({ customer, deliveryAddressTypes, closeModal }) {
  const { data, setData, post, processing, errors, reset } = useForm({
    address_type: 1,
    post_code: '',
    address: '',
    company_name: '',
    contact_name: '',
    tel: '',
    note: '',
  });

  function submit(e) {
    e.preventDefault();
    post(route('customers.delivery-addresses.add', customer), {
      onSuccess: () => {
        reset();
        closeModal();
      }
    });
  };

  return (
    <>
      <form id="deliveryAddressCreateForm" onSubmit={submit}>
        <div className="table-wrapper">
          <table className="table">
            <tbody className="tbody">

              <TableRow className="is-flexible">
                <TableHeaderCell>
                  <FormLabel htmlFor="address_type-1" label="区分" isRequired={true} />
                </TableHeaderCell>
                <TableDataCell className="u-flex">
                  <RadioGroup
                    id="address_type"
                    options={deliveryAddressTypes}
                    value={data.address_type}
                    onChange={e => setData('address_type', parseInt(e.target.value))}
                    error={errors.address_type}
                  />
                  {errors.address_type && (<div className="invalid-feedback">{errors.address_type}</div>)}
                </TableDataCell>
              </TableRow>

              <TableInputRow
                labelName="郵便番号"
                inputName="post_code"
                data={data}
                errors={errors}
                setData={setData}
              />

              <TableInputRow
                labelName="住所"
                inputName="address"
                data={data}
                errors={errors}
                setData={setData} isRequired={true}
              />

              <TableInputRow
                labelName="会社名"
                inputName="company_name"
                data={data}
                errors={errors}
                setData={setData}
              />

              <TableInputRow
                labelName="担当者名"
                inputName="contact_name"
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

              <TableTextAreaRow
                labelName="備考"
                inputName="note"
                data={data}
                errors={errors}
                setData={setData}
                isRequired={false}
              />

            </tbody>
          </table>
        </div>
      </form>
      <button
        type="submit"
        form="deliveryAddressCreateForm"
        className="btn btn-primary u-mt-3"
        disabled={processing}
      >
        登録する
      </button>
    </>
  );
}

