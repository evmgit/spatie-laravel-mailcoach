@if(isset($src) || isset($html))
    <embedded-webview @isset($src) src="{{ $src }}" @endisset>
    </embedded-webview>

    <script>
        class EmbeddedWebview extends HTMLElement {
            connectedCallback() {
                @isset($html)
                    const shadow = this.attachShadow({ mode: 'closed' });
                    shadow.innerHTML = @json($html);
                @else
                    fetch(this.getAttribute('src'))
                    .then(response => response.text())
                    .then(html => {
                        const shadow = this.attachShadow({ mode: 'closed' });
                        shadow.innerHTML = html;
                    });
                @endisset
            }
        }

        window.customElements.define('embedded-webview', EmbeddedWebview);
    </script>
@endif
