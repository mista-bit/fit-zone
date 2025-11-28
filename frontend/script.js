
document.querySelectorAll('a[href^="#"]').forEach(function(link) {
    link.addEventListener('click', function(evento) {
        evento.preventDefault();
        const destino = document.querySelector(this.getAttribute('href'));
        
        if (destino) {
            destino.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
            
        }
    });
});

const botaoVoltarTopo = document.getElementById('botaoVoltarTopo');

if (botaoVoltarTopo) {
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            botaoVoltarTopo.classList.remove('opacity-0', 'pointer-events-none');
            botaoVoltarTopo.classList.add('opacity-100');
        } else {
            botaoVoltarTopo.classList.add('opacity-0', 'pointer-events-none');
            botaoVoltarTopo.classList.remove('opacity-100');
        }
    });

    botaoVoltarTopo.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
}

const barraNavegacao = document.querySelector('nav');

if (barraNavegacao) {
    window.addEventListener('scroll', function() {
        if (window.scrollY > 50) {
            barraNavegacao.classList.add('bg-gray-900');
            barraNavegacao.classList.remove('bg-gray-900/95');
        } else {
            barraNavegacao.classList.remove('bg-gray-900');
            barraNavegacao.classList.add('bg-gray-900/95');
        }
    });
}

function animarContador(elemento, valorFinal, duracao = 2000) {
    if (!elemento) return;
    
    let valorAtual = 0;
    const incremento = valorFinal / (duracao / 16);
    const sufixo = valorFinal >= 1000 ? '+' : '';
    
    const temporizador = setInterval(function() {
        valorAtual += incremento;
        if (valorAtual >= valorFinal) {
            elemento.textContent = valorFinal + sufixo;
            clearInterval(temporizador);
        } else {
            elemento.textContent = Math.floor(valorAtual) + sufixo;
        }
    }, 16);
}

const secaoEstatisticas = document.querySelector('#estatisticas');
if (secaoEstatisticas) {
    const observadorEstatisticas = new IntersectionObserver(function(entradas) {
        entradas.forEach(function(entrada) {
            if (entrada.isIntersecting) {
                const contadores = secaoEstatisticas.querySelectorAll('[data-contador]');
                contadores.forEach(function(contador) {
                    const valorFinal = parseInt(contador.getAttribute('data-contador'));
                    if (valorFinal && contador.textContent === '0') {
                        animarContador(contador, valorFinal);
                    }
                });
                observadorEstatisticas.unobserve(entrada.target);
            }
        });
    }, { threshold: 0.5 });
    
    observadorEstatisticas.observe(secaoEstatisticas);
}

const formularioContato = document.getElementById('formularioContato');

if (formularioContato) {
    formularioContato.addEventListener('submit', function(evento) {
        evento.preventDefault();
        
        const campoNome = document.getElementById('nome');
        const campoEmail = document.getElementById('email');
        const campoMensagem = document.getElementById('mensagem');
        
        const nome = campoNome ? campoNome.value.trim() : '';
        const email = campoEmail ? campoEmail.value.trim() : '';
        const mensagem = campoMensagem ? campoMensagem.value.trim() : '';
        
        if (nome && email && mensagem) {
            
            alert(`Obrigado, ${nome}! Sua mensagem foi enviada com sucesso. Entraremos em contato em breve!`);
            formularioContato.reset();
        } else {
            alert('Por favor, preencha todos os campos corretamente.');
        }
    });
}

document.querySelectorAll('#planos button').forEach(function(botao) {
    botao.addEventListener('click', function() {
        const cardPlano = botao.closest('.bg-gray-800, .bg-gradient-to-br');
        const nomePlano = cardPlano ? cardPlano.querySelector('h3').textContent : 'Plano';
        
        alert(`Você escolheu o plano ${nomePlano}! Em breve entraremos em contato.`);
        
    });
});

const botoesHero = document.querySelectorAll('#inicio button');

if (botoesHero.length > 0) {
    botoesHero[0].addEventListener('click', function() {
        const secaoPlanos = document.querySelector('#planos');
        if (secaoPlanos) {
            secaoPlanos.scrollIntoView({ behavior: 'smooth' });
        }
    });
    
    if (botoesHero.length > 1) {
        botoesHero[1].addEventListener('click', function() {
            const secaoSobre = document.querySelector('#sobre');
            if (secaoSobre) {
                secaoSobre.scrollIntoView({ behavior: 'smooth' });
            }
        });
    }
}

const opcoesObservador = {
    threshold: 0.1,
    rootMargin: '0px 0px -100px 0px'
};

const observador = new IntersectionObserver(function(entradas) {
    entradas.forEach(function(entrada) {
        if (entrada.isIntersecting) {
            entrada.target.classList.remove('opacity-0');
            if (!entrada.target.classList.contains('animate-fade-in-up') && 
                !entrada.target.classList.contains('animate-slide-left') &&
                !entrada.target.classList.contains('animate-slide-right') &&
                !entrada.target.classList.contains('animate-scale-in')) {
                entrada.target.classList.add('animate-fade-in-up');
            }
            observador.unobserve(entrada.target);
        }
    });
}, opcoesObservador);

document.querySelectorAll('.opacity-0').forEach(function(elemento) {
    observador.observe(elemento);
});

function animarContador(elemento, valorFinal, duracao = 2000) {
    if (!elemento) return;
    
    let valorAtual = 0;
    const incremento = valorFinal / (duracao / 16);
    
    const temporizador = setInterval(function() {
        valorAtual += incremento;
        if (valorAtual >= valorFinal) {
            elemento.textContent = valorFinal;
            clearInterval(temporizador);
        } else {
            elemento.textContent = Math.floor(valorAtual);
        }
    }, 16);
}

function efeitoDigitacao(elemento, texto, velocidade = 100) {
    if (!elemento) return;
    
    let indice = 0;
    elemento.textContent = '';
    
    function digitar() {
        if (indice < texto.length) {
            elemento.textContent += texto.charAt(indice);
            indice++;
            setTimeout(digitar, velocidade);
        }
    }
    
    digitar();
}


document.addEventListener('DOMContentLoaded', function() {
    console.log('Landing page da Fitzone carregada com sucesso!');
});
