const friendsList = document.getElementById("friends-list");

if (friendsList) {
    const friendsCount = document.getElementById("friends-count");
    const emptyState = document.getElementById("friends-empty-state");
    const notice = document.getElementById("friends-notice");

    const updateFriendsCount = () => {
        const count = friendsList.querySelectorAll("[data-friend-card]").length;
        friendsCount.textContent = `${count} ${count === 1 ? "friend" : "friends"}`;
        emptyState.hidden = count !== 0;
    };

    const showNotice = (message) => {
        notice.textContent = message;
        notice.hidden = false;
    };

    friendsList.addEventListener("click", (event) => {
        const messageButton = event.target.closest("[data-message-toggle]");
        if (messageButton) {
            const friendCard = messageButton.closest("[data-friend-card]");
            const messageForm = friendCard.querySelector("[data-message-form]");
            const isOpening = messageForm.hidden;

            friendsList.querySelectorAll("[data-message-form]").forEach((form) => {
                form.hidden = true;
                form.closest("[data-friend-card]")
                    .querySelector("[data-message-toggle]")
                    .setAttribute("aria-expanded", "false");
            });

            messageForm.hidden = !isOpening;
            messageButton.setAttribute("aria-expanded", String(isOpening));
            if (isOpening) {
                messageForm.querySelector("textarea").focus();
            }
            return;
        }

        const removeButton = event.target.closest("[data-remove-friend]");
        if (removeButton) {
            const friendCard = removeButton.closest("[data-friend-card]");
            const friendName = friendCard.querySelector(".friend-details h2").textContent;

            if (window.confirm(`Remove ${friendName} from your friends list?`)) {
                friendCard.remove();
                updateFriendsCount();
                showNotice(`${friendName} was removed from this demo list. The change is not saved to an account.`);
            }
            return;
        }

        const reportButton = event.target.closest("[data-report-friend]");
        if (reportButton) {
            const friendName = reportButton.closest("[data-friend-card]")
                .querySelector(".friend-details h2").textContent;
            showNotice(`Reporting ${friendName} is not connected yet. No report was sent.`);
        }
    });

    friendsList.addEventListener("submit", (event) => {
        const messageForm = event.target.closest("[data-message-form]");
        if (!messageForm) {
            return;
        }

        event.preventDefault();
        const friendName = messageForm.closest("[data-friend-card]")
            .querySelector(".friend-details h2").textContent;
        const message = messageForm.querySelector("textarea").value.trim();
        if (!message) {
            messageForm.querySelector("textarea").focus();
            return;
        }

        showNotice(`Message preview for ${friendName}: “${message}” — it was not delivered or saved.`);
        messageForm.reset();
        messageForm.hidden = true;
        messageForm.closest("[data-friend-card]")
            .querySelector("[data-message-toggle]")
            .setAttribute("aria-expanded", "false");
    });

    updateFriendsCount();
}
