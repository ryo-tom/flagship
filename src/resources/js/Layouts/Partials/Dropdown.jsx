import { Link } from "@inertiajs/react";

export default function Dropdown() {
  return (
    <div className="sidebar-dropdown">
      <div className="dropdown-inner">
        <ul className="dropdown-list">
          <li className="dropdown-item">
            <Link href="#" className="dropdown-link">Menu1</Link>
          </li>
          <li className="dropdown-item">
            <Link href="#" className="dropdown-link">Menu2</Link>
          </li>
          <li className="dropdown-item">
            <Link method="post" href={route('logout')} className="dropdown-link" as="button">
              Log out
            </Link>
          </li>
        </ul>
      </div>
    </div>
  );
}
