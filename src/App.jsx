import Navbar from './components/Navbar'
import './App.css'

function App() {
  return (
    <div className="App">
      <Navbar />
      
      <section id="home" className="section home-section">
        <div className="container">
          <h1>Bem-vindo à Fit Zone</h1>
          <p>Sua jornada para um corpo saudável começa aqui!</p>
        </div>
      </section>

      <section id="planos" className="section planos-section">
        <div className="container">
          <h2>Nossos Planos</h2>
          <p>Escolha o plano ideal para você</p>
        </div>
      </section>

      <section id="clientes" className="section clientes-section">
        <div className="container">
          <h2>Nossos Clientes</h2>
          <p>Veja o que nossos clientes estão dizendo</p>
        </div>
      </section>

      <section id="cadastre-se" className="section cadastro-section">
        <div className="container">
          <h2>Cadastre-se</h2>
          <p>Comece sua transformação hoje mesmo!</p>
        </div>
      </section>
    </div>
  )
}

export default App

