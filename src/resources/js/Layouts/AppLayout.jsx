import { useState } from 'react'
import Sidebar from './Partials/Sidebar';
import MenuIcon from '@mui/icons-material/Menu';
import AccountCircleOutlinedIcon from '@mui/icons-material/AccountCircleOutlined';

export default function AppLayout({ children }) {
  const [isSidebarOpen, setIsSidebarOpen] = useState(true);

  const toggleSidebar = () => {
    setIsSidebarOpen(!isSidebarOpen);
  };

  return (
    <div className="page-wrapper">
      <header className="mobile-header">
        <button className="nav-toggler">
          <MenuIcon className="header-menu-icon" />
        </button>
        <div className="header-title">Sales Manager+</div>
        <button className="header-account-nav">
          <AccountCircleOutlinedIcon className="header-account-icon" />
        </button>
      </header>
      <Sidebar isOpen={isSidebarOpen} onToggle={toggleSidebar} />
      <main className={`main-container ${!isSidebarOpen ? 'is-expanded' : ''}`}>
        {children}
      </main>
    </div>
  );
}
