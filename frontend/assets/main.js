(() => {
const mainApp = document.querySelector(".main-app");

if (mainApp) {
    const sidebarToggle = document.getElementById("sidebar-toggle");
    const sidebar = document.getElementById("main-sidebar");
    const sidebarBackdrop = document.getElementById("sidebar-backdrop");
    const sidebarLinks = sidebar.querySelectorAll(".main-sidebar-link");
    const mainContent = document.querySelector(".main-content");
    const quickMenu = document.getElementById("quick-menu");
    const commentsDrawer = document.getElementById("comments-drawer");
    const commentsBackdrop = document.getElementById("comments-backdrop");
    const commentsClose = document.getElementById("comments-close");
    const commentsTitle = document.getElementById("comments-drawer-title");
    const commentsPostTitle = document.getElementById("comments-post-title");
    const commentsList = document.getElementById("comments-list");
    const commentButtons = mainApp.querySelectorAll("[data-open-comments]");
    const profileLink = mainApp.querySelector(".main-profile-link");
    const userStatus = document.getElementById("user-status");
    const statusChoices = mainApp.querySelectorAll("[data-status-choice]");
    const statusNames = {
        available: "Available",
        away: "Away",
        busy: "Busy"
    };
    const statusClasses = {
        available: "status-green",
        away: "status-yellow",
        busy: "status-red"
    };
    let commentTrigger = null;

    const setSidebarOpen = (isOpen) => {
        mainApp.classList.toggle("sidebar-open", isOpen);
        sidebarToggle.setAttribute("aria-expanded", String(isOpen));
        sidebarToggle.setAttribute("aria-label", isOpen ? "Close navigation menu" : "Open navigation menu");
        sidebar.setAttribute("aria-hidden", String(!isOpen));
        sidebar.inert = !isOpen;

        if (isOpen) {
            sidebarBackdrop.hidden = false;
            const firstLink = sidebar.querySelector("a");
            if (firstLink) {
                firstLink.focus();
            }
        } else {
            sidebarToggle.focus();
        }
    };

    const mainNavbar = mainApp.querySelector(".main-navbar");

    sidebarToggle.addEventListener("click", () => {
        setSidebarOpen(sidebarToggle.getAttribute("aria-expanded") !== "true");
    });

    sidebarBackdrop.addEventListener("click", () => setSidebarOpen(false));
    sidebarBackdrop.addEventListener("transitionend", (event) => {
        if (event.propertyName === "opacity" && sidebarToggle.getAttribute("aria-expanded") !== "true") {
            sidebarBackdrop.hidden = true;
        }
    });
    sidebarLinks.forEach((link) => {
        link.addEventListener("click", () => setSidebarOpen(false));
    });

    const closeComments = (restoreFocus = true) => {
        mainApp.classList.remove("comments-open");
        commentsDrawer.setAttribute("aria-hidden", "true");
        commentsDrawer.inert = true;
        commentButtons.forEach((button) => button.setAttribute("aria-expanded", "false"));
        mainNavbar.inert = false;
        mainContent.inert = false;

        if (restoreFocus && commentTrigger) {
            commentTrigger.focus();
        }
    };

    const openComments = (button) => {
        const template = document.getElementById(button.dataset.commentTemplate);
        if (!template) {
            return;
        }

        if (sidebarToggle.getAttribute("aria-expanded") === "true") {
            setSidebarOpen(false);
        }
        quickMenu.open = false;
        commentTrigger = button;
        commentButtons.forEach((commentButton) => {
            commentButton.setAttribute("aria-expanded", String(commentButton === button));
        });
        commentsList.replaceChildren(template.content.cloneNode(true));
        commentsTitle.textContent = "CV comments";
        const postCard = button.closest("[data-post-card]");
        const postAuthor = postCard.querySelector(".main-post-author h3").textContent;
        const postType = postCard.querySelector(".main-post-type").textContent;
        commentsPostTitle.textContent = `${postAuthor} · ${postType}`;
        commentsDrawer.setAttribute("aria-hidden", "false");
        commentsDrawer.inert = false;
        commentsBackdrop.hidden = false;
        mainNavbar.inert = true;
        mainContent.inert = true;
        mainApp.classList.add("comments-open");
        commentsClose.focus();
    };

    commentButtons.forEach((button) => {
        button.addEventListener("click", () => {
            if (button.getAttribute("aria-expanded") === "true") {
                closeComments();
            } else {
                openComments(button);
            }
        });
    });

    commentsClose.addEventListener("click", () => closeComments());
    commentsBackdrop.addEventListener("click", () => closeComments());
    commentsBackdrop.addEventListener("transitionend", (event) => {
        if (event.target === commentsBackdrop && event.propertyName === "opacity"
            && !mainApp.classList.contains("comments-open")) {
            commentsBackdrop.hidden = true;
        }
    });

    statusChoices.forEach((choice) => {
        choice.addEventListener("click", () => {
            const status = choice.dataset.statusChoice;
            if (!statusNames[status]) {
                return;
            }

            userStatus.classList.remove("status-green", "status-yellow", "status-red");
            userStatus.classList.add(statusClasses[status]);
            userStatus.setAttribute("aria-label", `Status: ${statusNames[status]}`);
            profileLink.setAttribute("aria-label", `Open your profile. Status: ${statusNames[status]}`);

            statusChoices.forEach((statusChoice) => {
                statusChoice.setAttribute("aria-pressed", String(statusChoice === choice));
            });
        });
    });

    mainApp.addEventListener("click", (event) => {
        if (quickMenu.open && !quickMenu.contains(event.target)) {
            quickMenu.open = false;
        }
    });

    mainApp.addEventListener("keydown", (event) => {
        if (event.key === "Tab" && mainApp.classList.contains("comments-open")) {
            const focusableElements = commentsDrawer.querySelectorAll(
                "button:not([disabled]), a[href], input:not([disabled]), [tabindex]:not([tabindex=\"-1\"])"
            );
            const firstElement = focusableElements[0];
            const lastElement = focusableElements[focusableElements.length - 1];

            if (event.shiftKey && document.activeElement === firstElement) {
                event.preventDefault();
                lastElement.focus();
            } else if (!event.shiftKey && document.activeElement === lastElement) {
                event.preventDefault();
                firstElement.focus();
            }
        }

        if (event.key === "Escape") {
            if (mainApp.classList.contains("comments-open")) {
                closeComments();
            }
            if (sidebarToggle.getAttribute("aria-expanded") === "true") {
                setSidebarOpen(false);
            }
            quickMenu.open = false;
        }
    });

    mainApp.querySelectorAll("[data-post-card]").forEach((postCard) => {
        const actionButtons = postCard.querySelectorAll("[data-post-action]");

        actionButtons.forEach((button) => {
            button.addEventListener("click", () => {
                const action = button.dataset.postAction;
                const countName = action === "dislike" ? "dislikes" : `${action}s`;
                const countElement = postCard.querySelector(`[data-post-count="${countName}"]`);

                if (!countElement) {
                    return;
                }

                const currentCount = Number(countElement.textContent);
                if (!Number.isFinite(currentCount)) {
                    return;
                }

                if (action === "like" || action === "dislike") {
                    const wasPressed = button.getAttribute("aria-pressed") === "true";
                    const oppositeAction = action === "like" ? "dislike" : "like";
                    const oppositeButton = postCard.querySelector(`[data-post-action="${oppositeAction}"]`);
                    const oppositeCountName = oppositeAction === "dislike" ? "dislikes" : "likes";
                    const oppositeCount = postCard.querySelector(`[data-post-count="${oppositeCountName}"]`);

                    if (wasPressed) {
                        countElement.textContent = String(currentCount - 1);
                        button.setAttribute("aria-pressed", "false");
                    } else {
                        countElement.textContent = String(currentCount + 1);
                        button.setAttribute("aria-pressed", "true");

                        if (oppositeButton.getAttribute("aria-pressed") === "true") {
                            oppositeCount.textContent = String(Number(oppositeCount.textContent) - 1);
                            oppositeButton.setAttribute("aria-pressed", "false");
                        }
                    }
                } else if (action === "download") {
                    countElement.textContent = String(currentCount + 1);
                    const author = postCard.querySelector(".main-post-author h3").textContent;
                    const role = postCard.querySelector(".main-post-type").textContent;
                    const cvContents = [
                        `${author} - ${role}`,
                        "",
                        "This sample CV is a preview from the CV Maker community feed.",
                        "Replace this example with a real downloadable CV when post data is connected."
                    ].join("\n");
                    const downloadUrl = URL.createObjectURL(new Blob([cvContents], { type: "text/plain" }));
                    const downloadLink = document.createElement("a");
                    downloadLink.href = downloadUrl;
                    downloadLink.download = `${author.toLowerCase().replace(/\s+/g, "-")}-sample-cv.txt`;
                    document.body.appendChild(downloadLink);
                    downloadLink.click();
                    downloadLink.remove();
                    window.setTimeout(() => URL.revokeObjectURL(downloadUrl), 1000);
                }
            });
        });
    });
}
})();
