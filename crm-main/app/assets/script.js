const showRegisterProposalFormBtn = document.querySelector("#showRegisterProposalFormBtn");
const showRegisterClientFormBtn = document.querySelector("#showRegisterClientFormBtn");
const cancelRegisterProposalBtn = document.querySelector("#cancelRegisterProposalBtn");
const cancelRegisterClientBtn = document.querySelector("#cancelRegisterClientBtn");
const formWrapper = document.querySelector(".formWrapper");

showRegisterProposalFormBtn !== null ? showRegisterProposalFormBtn.addEventListener("click", () => showModal(formWrapper)) : null;
showRegisterClientFormBtn !== null ? showRegisterClientFormBtn.addEventListener("click", () => showModal(formWrapper)) : null;
cancelRegisterProposalBtn !== null ? cancelRegisterProposalBtn.addEventListener("click", () => hideModal(formWrapper)) : null;
cancelRegisterClientBtn !== null ? cancelRegisterClientBtn.addEventListener("click", () => hideModal(formWrapper)) : null;

function showModal(modal)
{
	modal.style.display = "flex";
}

function hideModal(modal)
{
	modal.style.display = "none";
}
