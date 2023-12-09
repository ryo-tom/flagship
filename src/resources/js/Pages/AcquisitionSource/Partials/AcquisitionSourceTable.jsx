export default function AcquisitionSourceTable({ acquisitionSources }) {
  return (
    <div className="table-wrapper is-scrollable">
      <table className="table">
        <thead className="table-header is-sticky">
          <tr className="table-row">
            <th className="th-cell col-fixed">ID</th>
            <th className="th-cell">表示順</th>
            <th className="th-cell">獲得元名</th>
          </tr>
        </thead>
        <tbody className="table-body">
          {acquisitionSources.map(acquisitionSource => (
            <tr key={acquisitionSource.id} className="table-row is-hoverable">
              <td className="td-cell u-w-80">{acquisitionSource.id}</td>
              <td className="td-cell u-w-96">{acquisitionSource.display_order}</td>
              <td className="td-cell">{acquisitionSource.name}</td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}
