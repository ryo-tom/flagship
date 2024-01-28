import AppLayout from '@/Layouts/AppLayout'

const Home = ({ inquiriesCountByStatus, totalInquiriesCount }) => {
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
    </>
  );
}

Home.layout = page => <AppLayout title="HOME" children={page} />

export default Home
