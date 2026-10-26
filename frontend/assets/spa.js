(() => {
    const app = document.getElementById("app");
    const viewFiles = {
        welcome: "welcome.html",
        login: "login.html",
        signup: "signup.html",
        main: "main.html",
        admin: "admain.html",
        friends: "friends.html",
        profile: "profile.html"
    };
    const loadedAssets = [];
    let activeRoute = null;
    let navigationId = 0;

    const parseHash = (hash) => {
        if (!hash.startsWith("#/")) {
            return { route: "welcome", anchor: hash.slice(1) };
        }

        const routeAndAnchor = hash.slice(2);
        const anchorIndex = routeAndAnchor.indexOf("#");
        return {
            route: anchorIndex === -1 ? routeAndAnchor : routeAndAnchor.slice(0, anchorIndex),
            anchor: anchorIndex === -1 ? "" : routeAndAnchor.slice(anchorIndex + 1)
        };
    };

    const showError = (error) => {
        console.error("Unable to load the requested CVer view.", error);
        const message = document.createElement("p");
        message.setAttribute("role", "alert");
        message.textContent = "This page could not be loaded. Please try again.";
        app.replaceChildren(message);
        app.setAttribute("aria-busy", "false");
    };

    const appendStylesheets = async (viewDocument, viewUrl, requestId) => {
        const nextStylesheets = Array.from(
            viewDocument.head.querySelectorAll('link[rel~="stylesheet"][href]')
        ).map((stylesheet) => {
            const link = document.createElement("link");
            link.rel = "stylesheet";
            link.href = new URL(stylesheet.getAttribute("href"), viewUrl).href;
            link.dataset.spaAsset = "";
            return link;
        });

        try {
            await Promise.all(nextStylesheets.map((link) => new Promise((resolve, reject) => {
                link.addEventListener("load", resolve, { once: true });
                link.addEventListener("error", () => reject(new Error(`Could not load stylesheet: ${link.href}`)), {
                    once: true
                });
                document.head.append(link);
            })));
        } catch (error) {
            nextStylesheets.forEach((link) => link.remove());
            throw error;
        }

        if (requestId !== navigationId) {
            nextStylesheets.forEach((link) => link.remove());
            return false;
        }

        loadedAssets.forEach((asset) => asset.remove());
        loadedAssets.splice(0, loadedAssets.length, ...nextStylesheets);
        return true;
    };

    const loadViewScripts = async (viewDocument, viewUrl, requestId) => {
        loadedAssets.filter((asset) => asset.tagName === "SCRIPT").forEach((script) => script.remove());
        for (const viewScript of viewDocument.querySelectorAll("script")) {
            if (requestId !== navigationId) {
                return false;
            }

            const script = document.createElement("script");
            if (viewScript.type) {
                script.type = viewScript.type;
            }

            if (viewScript.src) {
                script.src = new URL(viewScript.getAttribute("src"), viewUrl).href;
                script.async = false;
                await new Promise((resolve, reject) => {
                    script.addEventListener("load", resolve, { once: true });
                    script.addEventListener("error", () => reject(new Error(`Could not load script: ${script.src}`)), {
                        once: true
                    });
                    document.head.append(script);
                });
            } else {
                script.textContent = viewScript.textContent;
                document.head.append(script);
            }

            script.dataset.spaAsset = "";
            loadedAssets.push(script);
        }
        return requestId === navigationId;
    };

    const scrollToAnchor = (anchor) => {
        if (!anchor) {
            window.scrollTo(0, 0);
            return;
        }
        document.getElementById(anchor)?.scrollIntoView();
    };

    const renderRoute = async () => {
        const requestId = ++navigationId;
        const { route, anchor } = parseHash(window.location.hash);
        const viewFile = viewFiles[route];
        if (!viewFile) {
            showError(new Error(`Unknown view route: ${route}`));
            return;
        }
        app.setAttribute("aria-busy", "true");

        try {
            const viewUrl = new URL(viewFile, new URL("views/", window.location.href));
            const response = await fetch(viewUrl);
            if (!response.ok) {
                throw new Error(`View request failed with status ${response.status}: ${viewUrl.href}`);
            }

            const viewDocument = new DOMParser().parseFromString(await response.text(), "text/html");
            if (requestId !== navigationId) {
                return;
            }

            viewDocument.body.querySelectorAll("[src], [poster]").forEach((element) => {
                ["src", "poster"].forEach((attribute) => {
                    const value = element.getAttribute(attribute);
                    if (value) {
                        element.setAttribute(attribute, new URL(value, viewUrl).href);
                    }
                });
            });
            app.innerHTML = viewDocument.body.innerHTML;
            document.title = viewDocument.title || "CVer";

            if (!await appendStylesheets(viewDocument, viewUrl, requestId)
                || !await loadViewScripts(viewDocument, viewUrl, requestId)) {
                return;
            }

            activeRoute = route;
            app.setAttribute("aria-busy", "false");
            scrollToAnchor(anchor);
        } catch (error) {
            if (requestId === navigationId) {
                showError(error);
            }
        }
    };

    app.addEventListener("click", (event) => {
        if (!(event.target instanceof Element)) {
            return;
        }

        const link = event.target.closest("a[href]");
        if (!link || event.defaultPrevented || event.button !== 0
            || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey
            || (link.target && link.target !== "_self") || link.hasAttribute("download")) {
            return;
        }

        const href = link.getAttribute("href");
        if (href.startsWith("#/")) {
            const { route } = parseHash(href);
            if (viewFiles[route]) {
                event.preventDefault();
                window.location.hash = href;
            }
            return;
        }

        if (href.startsWith("#")) {
            event.preventDefault();
            if (href.length > 1) {
                window.location.hash = `#/${activeRoute || "welcome"}${href}`;
            }
        }
    });

    window.addEventListener("hashchange", () => {
        const { route, anchor } = parseHash(window.location.hash);
        if (route === activeRoute) {
            scrollToAnchor(anchor);
        } else {
            renderRoute();
        }
    });

    renderRoute();
})();
