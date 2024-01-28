import AppLayout from '@/Layouts/AppLayout'
import { formatNumber, formatCurrency } from '@/Utils/priceCalculator';

const Home = ({ inquiriesCountByStatus, totalInquiriesCount, inquiriesCountByType, salesOrdersCount, salesOrdersTotalSum, purchaseOrdersTotalSum }) => {
  return (
    <>
      <h1>HOME</h1>
      <p>Welcome to Sales Manager+</p>


      <div className="u-my-4">
        <div>当月 問い合わせ状況</div>
        {inquiriesCountByStatus.map(inquiry => (
          <p key={inquiry.status}>
            <span className={`inquiry-status status-${inquiry.status} u-mr-1`}>
              {inquiry.status_label}
            </span>
            <span>
              : {inquiry.count}件
            </span>
          </p>
        ))}
        <div>合計 : {totalInquiriesCount}件</div>
      </div>

      <div className="u-my-4">
        <div>当月 問い合わせ状況</div>
        {inquiriesCountByType.map(inquiry => (
          <p key={inquiry.inquiry_type_id}>
            <span className={`custom-label ${inquiry.inquiry_type.custom_label}`}>
              {inquiry.inquiry_type.name}
            </span>
            <span>
              : {inquiry.count}件
            </span>
          </p>
        ))}
        <div>合計 : {totalInquiriesCount}件</div>
      </div>

      <div className="u-my-4">
        <div>当月 受発注 <span className="u-text-sm">(納品日ベース)</span></div>
        <div>受注件数 : {formatNumber(salesOrdersCount)}件</div>
        <div>受注金額 : {formatCurrency(salesOrdersTotalSum)}</div>
        <div>発注金額 : {formatCurrency(purchaseOrdersTotalSum)}</div>
        <div>利益合計 : {formatCurrency(salesOrdersTotalSum - purchaseOrdersTotalSum)}</div>
      </div>


    </>
  );
}

Home.layout = page => <AppLayout title="HOME" children={page} />

export default Home
