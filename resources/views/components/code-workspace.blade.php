<style>
    .code-workspace {
        display: none; /* hidden by default */
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: #1e1e1e;
        z-index: 99999;
        flex-direction: column;
        font-family: 'Inter', sans-serif;
    }

    .code-workspace.active {
        display: flex;
    }

    .workspace-header {
        height: 50px;
        background: #181818;
        border-bottom: 1px solid #2d2d2d;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 20px;
        color: #fff;
        flex-shrink: 0;
    }

    .workspace-title {
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 600;
        font-size: 1.3rem;
        color: #ccc;
    }

    .workspace-title ion-icon {
        color: #ff4500;
        font-size: 1.6rem;
    }

    .workspace-btn {
        background: #ff4500;
        color: #fff;
        border: none;
        padding: 6px 14px;
        border-radius: 6px;
        font-size: 1.1rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        transition: background 0.2s;
    }

    .workspace-btn:hover {
        background: #e03e00;
    }

    .workspace-grid {
        flex: 1;
        display: grid;
        grid-template-columns: var(--video-width, calc(45% - 2px)) 4px 1fr;
        grid-template-rows: 1fr 4px var(--console-height, calc(30% - 2px));
        grid-template-areas: 
            "video v-resizer editor"
            "h-resizer h-resizer h-resizer"
            "console console console";
        height: calc(100vh - 50px);
        overflow: hidden;
    }

    /* Prevent iframe from eating mouse events while dragging */
    .workspace-grid.is-dragging iframe {
        pointer-events: none;
    }

    .v-resizer {
        grid-area: v-resizer;
        background: #252526;
        cursor: col-resize;
        transition: background 0.2s;
        z-index: 10;
        border-left: 1px solid #111;
        border-right: 1px solid #333;
    }
    
    .v-resizer:hover, .v-resizer.dragging {
        background: #ff4500;
    }

    .h-resizer {
        grid-area: h-resizer;
        background: #252526;
        cursor: row-resize;
        transition: background 0.2s;
        z-index: 10;
        border-top: 1px solid #111;
        border-bottom: 1px solid #333;
    }

    .h-resizer:hover, .h-resizer.dragging {
        background: #ff4500;
    }

    .workspace-video-area {
        grid-area: video;
        background: #000;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }

    .workspace-video-area #player {
        width: 100% !important;
        height: 100% !important;
    }

    .workspace-editor-area {
        grid-area: editor;
        background: #1e1e1e;
        position: relative;
        overflow: hidden;
        display: flex; /* For VS Code Sidebar + Main */
    }

    /* VS Code Sidebar Styles */
    .vscode-sidebar {
        width: 220px;
        background: #181818; /* Darker than editor */
        border-right: 1px solid #2d2d2d;
        display: flex;
        flex-direction: column;
        flex-shrink: 0;
    }

    .sidebar-section {
        margin-bottom: 15px;
    }

    .sidebar-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 15px;
        color: #bbb;
        font-size: 0.85rem;
        font-weight: 700;
        letter-spacing: 0.5px;
    }

    .sidebar-actions {
        display: flex;
        gap: 8px;
    }
    
    .sidebar-actions ion-icon {
        cursor: pointer;
        font-size: 1.1rem;
    }
    .sidebar-actions ion-icon:hover {
        color: #fff;
    }

    .sidebar-files {
        display: flex;
        flex-direction: column;
    }

    .file-item {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 5px 15px 5px 25px;
        color: #ccc;
        font-size: 1rem;
        cursor: pointer;
        user-select: none;
    }

    .file-item:hover {
        background: #2a2d2e;
    }

    .file-item.active {
        background: #37373d;
        color: #fff;
    }

    .file-item ion-icon {
        font-size: 1.2rem;
    }

    /* VS Code Main Editor Area Styles */
    .vscode-main {
        flex: 1;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        background: #1e1e1e;
    }

    .vscode-tabs,
    .editor-tabs {
        height: 38px;
        background: #1e1e1e;
        display: flex;
        overflow-x: auto;
        scrollbar-width: none;
        flex-shrink: 0;
    }
    .vscode-tabs::-webkit-scrollbar { display: none; }

    .vscode-tab,
    .editor-tab {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 0 15px;
        background: #2d2d2d;
        color: #999;
        font-size: 1rem;
        cursor: pointer;
        border-right: 1px solid #1e1e1e;
        border-top: 2px solid transparent;
        min-width: 120px;
    }

    .vscode-tab.active,
    .editor-tab.active {
        background: #1e1e1e;
        color: #fff;
        border-top: 2px solid #ff4500;
    }

    .close-tab {
        margin-left: auto;
        border-radius: 3px;
        padding: 2px;
    }
    .close-tab:hover {
        background: #444;
    }

    .vscode-editor-container,
    .editor-container {
        flex: 1;
        width: 100%;
        min-height: 0;
        position: relative;
    }

    /* Console Styles */
    .workspace-console-area {
        grid-area: console;
        background: #1e1e1e;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .console-header {
        height: 35px;
        background: #1e1e1e;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 20px;
        color: #e7e7e7;
        font-size: 1rem;
        font-weight: 600;
        flex-shrink: 0;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .console-header-tabs {
        display: flex;
        gap: 20px;
    }
    .console-header-tab {
        color: #999;
        font-size: 0.95rem;
        cursor: pointer;
        padding-bottom: 8px;
        border-bottom: 1px solid transparent;
    }
    .console-header-tab.active {
        color: #e7e7e7;
        border-bottom: 1px solid #ff4500;
    }

    .clear-console-btn {
        background: none;
        border: none;
        color: #888;
        cursor: pointer;
        font-size: 1.4rem;
        display: flex;
        align-items: center;
        transition: color 0.2s;
    }

    .clear-console-btn:hover {
        color: #fff;
    }

    .console-body {
        flex: 1;
        padding: 15px 20px;
        font-family: 'Fira Code', 'Courier New', Courier, monospace;
        font-size: 1rem;
        color: #d4d4d4;
        overflow-y: auto;
        border-top: 1px solid #2d2d2d;
    }

    .console-line {
        margin-bottom: 6px;
        line-height: 1.4;
    }
    
    .console-line.success {
        color: #4caf50;
    }
    
    .console-prompt {
        color: #ff4500;
        margin-right: 8px;
    }
</style>

<div id="code-workspace" class="code-workspace" x-data="codeWorkspace"
    :class="{ 'active': isOpen }"
    @open-workspace.window="open()"
    @mousemove.window="handleMouseMove($event)"
    @mouseup.window="handleMouseUp()">
    <div class="workspace-header">
        <div class="workspace-title">
            <ion-icon name="code-slash-outline"></ion-icon> SkillUp Workspace
        </div>
        <div class="workspace-actions">
            <button id="close-workspace-btn" class="workspace-btn" @click="close()">
                <ion-icon name="close-outline"></ion-icon> Exit Workspace
            </button>
        </div>
    </div>
    
    <div class="workspace-grid" id="workspace-grid">
        <!-- 1. Video Player Area -->
        <div class="workspace-video-area" id="workspace-video-container">
            <!-- YouTube player iframe will be moved here -->
        </div>

        <!-- Vertical Resizer -->
        <div class="v-resizer" id="v-resizer" @mousedown="startResizeV($event)" :class="{ 'dragging': isResizingV }"></div>
        
        <div class="workspace-editor-area" id="workspace-editor-area">
            <!-- VS Code Sidebar -->
            <div class="vscode-sidebar">
                <div class="sidebar-header">EXPLORER</div>
                <div class="sidebar-files">
                    <div class="file-item active">
                        <ion-icon name="logo-html5" style="color: #e34f26;"></ion-icon> index.html
                    </div>
                    <div class="file-item">
                        <ion-icon name="logo-css3" style="color: #1572B6;"></ion-icon> style.css
                    </div>
                    <div class="file-item">
                        <ion-icon name="logo-javascript" style="color: #f7df1e;"></ion-icon> script.js
                    </div>
                    
                    <div class="sidebar-header" style="margin-top: 20px;">DEPENDENCIES</div>
                    <div class="file-item">
                        <ion-icon name="cube-outline"></ion-icon> package.json
                    </div>
                </div>
            </div>
            
            <!-- VS Code Main Editor Area -->
            <div class="vscode-main">
                <div class="editor-tabs">
                    <div class="editor-tab active">
                        <ion-icon name="logo-html5" style="color: #e34f26;"></ion-icon> index.html
                    </div>
                </div>
                <div class="editor-container" id="monaco-editor-container">
                    <!-- Monaco Editor injected here -->
                </div>
            </div>
        </div>
        
        <!-- Horizontal Resizer -->
        <div class="h-resizer" id="h-resizer" @mousedown="startResizeH($event)" :class="{ 'dragging': isResizingH }"></div>

        <!-- 3. Console/Output Area -->
        <div class="workspace-console-area">
            <div class="console-header">
                <div class="console-tabs">
                    <div class="console-header-tab">PROBLEMS</div>
                    <div class="console-header-tab active">OUTPUT</div>
                    <div class="console-header-tab">DEBUG CONSOLE</div>
                    <div class="console-header-tab">TERMINAL</div>
                </div>
                <button class="clear-console-btn" id="clear-console-btn" title="Clear Console" @click="clearConsole()">
                    <ion-icon name="trash-outline"></ion-icon>
                </button>
            </div>
            <div class="console-body" id="console-output">
                <div class="console-line"><span class="console-prompt">~/skillup/project$</span> npm run dev</div>
                <div class="console-line success">VITE v5.0.0  ready in 250 ms</div>
                <div class="console-line"><br/></div>
                <div class="console-line">  ➜  Local:   http://localhost:5173/</div>
                <div class="console-line">  ➜  Network: use --host to expose</div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('codeWorkspace', () => ({
        isOpen: false,
        isResizingV: false,
        isResizingH: false,

        open() {
            const playerEl = document.getElementById('player');
            const workspaceVideoContainer = document.getElementById('workspace-video-container');
            if (playerEl && workspaceVideoContainer) {
                workspaceVideoContainer.appendChild(playerEl);
            }

            this.isOpen = true;
            document.body.style.overflow = 'hidden';

            if (!window.monacoEditor) {
                this.initMonaco();
            } else {
                setTimeout(() => window.monacoEditor.layout(), 100);
            }
        },

        close() {
            const playerEl = document.getElementById('player');
            const originalVideoContainer = document.querySelector('.video-box');
            if (playerEl && originalVideoContainer) {
                originalVideoContainer.appendChild(playerEl);
            }

            this.isOpen = false;
            document.body.style.overflow = '';
        },

        clearConsole() {
            const consoleOutput = document.getElementById('console-output');
            if (consoleOutput) {
                consoleOutput.innerHTML = '<div class="console-line"><span class="console-prompt">~/skillup/project$</span></div>';
            }
        },

        startResizeV(e) {
            this.isResizingV = true;
            e.preventDefault();
            document.getElementById('workspace-grid').classList.add('is-dragging');
        },

        startResizeH(e) {
            this.isResizingH = true;
            e.preventDefault();
            document.getElementById('workspace-grid').classList.add('is-dragging');
        },

        handleMouseMove(e) {
            const grid = document.getElementById('workspace-grid');
            if (!grid) return;

            if (this.isResizingV) {
                const newWidth = (e.clientX / window.innerWidth) * 100;
                if (newWidth > 15 && newWidth < 85) {
                    grid.style.setProperty('--video-width', 'calc(' + newWidth + '% - 2px)');
                }
            }
            if (this.isResizingH) {
                const gridHeight = window.innerHeight - 50;
                const newHeight = ((window.innerHeight - e.clientY) / gridHeight) * 100;
                if (newHeight > 10 && newHeight < 80) {
                    grid.style.setProperty('--console-height', 'calc(' + newHeight + '% - 2px)');
                }
            }
        },

        handleMouseUp() {
            if (this.isResizingV || this.isResizingH) {
                this.isResizingV = false;
                this.isResizingH = false;
                const grid = document.getElementById('workspace-grid');
                if (grid) grid.classList.remove('is-dragging');
            }
        },

        initMonaco() {
            if (window._workspaceMonacoInitStarted) return;
            window._workspaceMonacoInitStarted = true;

            const loaderScript = document.createElement('script');
            loaderScript.src = 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.43.0/min/vs/loader.min.js';
            loaderScript.onload = () => {
                require.config({ paths: { 'vs': 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.43.0/min/vs' }});
                require(['vs/editor/editor.main'], () => {
                    const container = document.getElementById('monaco-editor-container');
                    if (!container) return;

                    const defaultCode = [
                        '\x3Chtml\x3E',
                        '    \x3Chead\x3E',
                        '        \x3Clink rel="stylesheet" href="index.css"\x3E',
                        '    \x3C/head\x3E',
                        '    \x3Cbody\x3E',
                        '        \x3Ch1\x3EPeople entered:\x3C/h1\x3E',
                        '        \x3Ch2 id="count-el"\x3E0\x3C/h2\x3E',
                        '        \x3Cscr' + 'ipt\x3E',
                        '            document.getElementById("count-el").innerText = 5;',
                        '        \x3C/scr' + 'ipt\x3E',
                        '    \x3C/body\x3E',
                        '\x3C/html\x3E'
                    ].join('\n');

                    window.monacoEditor = monaco.editor.create(container, {
                        value: defaultCode,
                        language: 'html',
                        theme: 'vs-dark',
                        automaticLayout: true,
                        minimap: { enabled: false },
                        fontSize: 14,
                        fontFamily: "'Fira Code', 'Courier New', monospace"
                    });

                    if (window.ResizeObserver) {
                        const ro = new ResizeObserver(() => {
                            if (window.monacoEditor) {
                                window.monacoEditor.layout();
                            }
                        });
                        ro.observe(container);
                    }
                });
            };
            document.head.appendChild(loaderScript);
        }
    }));
});
</script>
