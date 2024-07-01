(function () {
    class FireView {
        constructor() {
            this.timeout = 30;
            this.isLoading = false;
            this.isError = false;
            this.actions = {
                beforeLoad: [],
                afterLoad: [],
                onError: [],
                onRender: [],
            };
        }

        on(action, callback) {
            this.actions[action].push(callback);
        }

        doAction(action) {
            this.actions[action].forEach((callback) => callback());
        }

        checkFireLinks() {
            document.querySelectorAll('a[fire]').forEach((fire) => {
                fire.hostname == location.hostname && fire.addEventListener('click', (e) => { e.preventDefault(), this.route(fire.pathname + fire.search) });
            });
        }

        checkFireForms() {
            document.querySelectorAll('form[fire]').forEach((fire) => {
                fire.addEventListener('submit', (e) => { e.preventDefault(), this.submitFireForm(fire) });
            });
        }

        checkFireNavigation() {
            window.addEventListener('popstate', (route) => route.state && this.updateDocument(route.state));
        }

        route(path) {
            !this.isLoading && this.currentPath() != path && this.renderFireContent(path);
        }

        submitFireForm(form) {
            if (this.isLoading) return;
            this.request(form.getAttribute('action'), form.getAttribute('method'), new FormData(form))
                .then((resp) => {
                    if (resp && resp.status && resp.status == 'success') {
                        form.reset();
                    }
                    if (resp && resp.redirect) {
                        window.location.href = resp.redirect;
                    } else if (resp && resp.push) {
                        this.route(resp.push);
                    } else if (resp && resp.status && resp.message) {
                        for (const child of form.children) {
                            child.attributes.fire && child.attributes.fire.nodeValue == resp.status && (child.innerHTML = resp.message);
                        }
                    }
                });
        }

        currentPath() {
            return window.location.pathname + window.location.search;
        }

        renderFireContent(path) {
            this.request(path)
                .then((data) => {
                    if (!this.isError && data) {
                        if (data.content) {
                            this.updateRouteHistory(path, data),
                                this.updateDocument(data);
                        } else if (data.push) {
                            this.route(data.push);
                        } else if (data.redirect) {
                            window.location.href = data.redirect;
                        }
                    }
                });
        }

        updateRouteHistory(path, data) {
            window.history.pushState(data, '', path);
        }

        updateDocument(data) {
            this.setDocumentTitle(data.title),
                this.setFireContent(data.content),
                this.setFireBlocks(data.blocks),
                this.checkFireLinks(),
                this.checkFireForms(),
                this.doAction('onRender');
        }

        setDocumentTitle(title) {
            document.title = title;
        }

        setFireContent(content) {
            document.querySelectorAll('[fire="content"]').forEach((div) => this.renderHtml(content, div));
        }

        setFireBlocks(blocks) {
            Object.entries(blocks).forEach(([key, value]) => {
                document.querySelectorAll('[fire="' + key + '"]').forEach((div) => this.renderHtml(value, div));
            });
        }

        renderHtml(html, target) {
            let div = document.createElement('div');
            div.innerHTML = html;

            let scripts = div.querySelectorAll('script');

            target.replaceChildren(...div.childNodes);

            for (let i = 0; i < scripts.length; i++) {
                let script = document.createElement('script');
                script.type = scripts[i].type || 'text/javascript';

                if (scripts[i].hasAttribute('src')) {
                    script.src = scripts[i].src;
                } else {
                    script.innerHTML = scripts[i].innerHTML;
                }

                document.head.appendChild(script);
                document.head.removeChild(script);
            }
        }

        async request(path, method = 'get', data = null) {
            this.isLoading = true;
            this.isError = false;
            this.doAction('beforeLoad');
            try {
                const resp = await fetch(path, {
                    method: method,
                    headers: {
                        "Accept": "application/json",
                        "Content-Agent": "fire-view"
                    },
                    redirect: "error",
                    body: data,
                    signal: AbortSignal.timeout(1000 * this.timeout),
                });
                return await resp.json()
                    .catch(error => this.fireError(error))
                    .finally(() => (this.isLoading = false, this.doAction('afterLoad')));
            } catch (error) {
                this.fireError(error);
            }
        }

        fireError(error) {
            this.isError = true, this.isLoading = false, this.doAction('afterLoad'), this.doAction('onError'), console.log('Fire Error: ' + error);
        }
    }

    window.fireView = new FireView();
    window.fireView.checkFireNavigation();
    window.fireView.checkFireLinks();
    window.fireView.checkFireForms();
})();
