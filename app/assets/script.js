const botaoMostrarCadastrarProposta = document.querySelector('#botaoMostrarCadastrarProposta');
const botaoMostrarCadastrarCliente = document.querySelector('#botaoMostrarCadastrarCliente');
const botaoCancelarCadastrarProposta = document.querySelector('#botaoCancelarCadastrarProposta');
const botaoCancelarCadastrarCliente = document.querySelector('#botaoCancelarCadastrarCliente');
const containerForm = document.querySelector('.containerForm');

botaoMostrarCadastrarProposta !== null ? botaoMostrarCadastrarProposta.addEventListener('click', () => showModal(containerForm)) : null;
botaoMostrarCadastrarCliente !== null ? botaoMostrarCadastrarCliente.addEventListener('click', () => showModal(containerForm)) : null;
botaoCancelarCadastrarProposta !== null ? botaoCancelarCadastrarProposta.addEventListener('click', () => hideModal(containerForm)) : null;
botaoCancelarCadastrarCliente !== null ? botaoCancelarCadastrarCliente.addEventListener('click', () => hideModal(containerForm)) : null;

function showModal(modal)
{
	modal.style.display = 'flex';
}

function hideModal(modal)
{
	modal.style.display = 'none';
}
