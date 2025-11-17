import { useState } from 'react'
import './Navbar.css'

function Navbar() {
  const [menuAberto, setMenuAberto] = useState(false)

  const alternarMenu = () => {
    setMenuAberto(!menuAberto)
  }

  return (
    <nav className="navbar">
      <div className="nav-container">
        <div className="nav-logo">
          <h2> Fit Zone</h2>
        </div>
        
        <ul className={`nav-menu ${menuAberto ? 'active' : ''}`}>
          <li className="nav-item">
            <a href="#home" className="nav-link" onClick={() => setMenuAberto(false)}>
              Home
            </a>
          </li>
          <li className="nav-item">
            <a href="#planos" className="nav-link" onClick={() => setMenuAberto(false)}>
              Planos
            </a>
          </li>
          <li className="nav-item">
            <a href="#clientes" className="nav-link" onClick={() => setMenuAberto(false)}>
              Clientes
            </a>
          </li>
          <li className="nav-item">
            <a href="#cadastre-se" className="nav-link nav-cta" onClick={() => setMenuAberto(false)}>
              Cadastre-se
            </a>
          </li>
        </ul>

        <div className="nav-toggle" onClick={alternarMenu}>
          <span className={menuAberto ? 'bar active' : 'bar'}></span>
          <span className={menuAberto ? 'bar active' : 'bar'}></span>
          <span className={menuAberto ? 'bar active' : 'bar'}></span>
        </div>
      </div>
    </nav>
  )
}

export default Navbar

