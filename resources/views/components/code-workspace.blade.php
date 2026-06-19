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
        background: #252526;
        border-bottom: 1px solid #333;
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
        font-size: 1.1rem;
        color: #ccc;
    }

    .workspace-title ion-icon {
        color: #ff4500;
        font-size: 1.4rem;
    }

    .workspace-btn {
        background: #ff4500;
        color: #fff;
        border: none;
        padding: 6px 14px;
        border-radius: 6px;
        font-size: 0.95rem;
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
        grid-template-columns: var(--video-width, calc(50% - 2px)) 4px 1fr;
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
        background: #333;
        cursor: col-resize;
        transition: background 0.2s;
        z-index: 10;
    }
    
    .v-resizer:hover, .v-resizer.dragging {
        background: #ff4500;
    }

    .h-resizer {
        grid-area: h-resizer;
        background: #333;
        cursor: row-resize;
        transition: background 0.2s;
        z-index: 10;
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
    }

    .workspace-console-area {
        grid-area: console;
        background: #1e1e1e;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .console-header {
        height: 35px;
        background: #252526;
        border-bottom: 1px solid #333;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 15px;
        color: #ccc;
        font-size: 0.9rem;
        font-weight: 600;
        flex-shrink: 0;
    }

    .clear-console-btn {
        background: none;
        border: none;
        color: #888;
        cursor: pointer;
        font-size: 1.1rem;
        display: flex;
        align-items: center;
        transition: color 0.2s;
    }

    .clear-console-btn:hover {
        color: #fff;
    }

    .console-body {
        flex: 1;
        padding: 10px 15px;
        font-family: 'Fira Code', 'Courier New', Courier, monospace;
        font-size: 0.9rem;
        color: #d4d4d4;
        overflow-y: auto;
    }

    .console-line {
        margin-bottom: 6px;
        line-height: 1.4;
    }
    
    .console-line.success {
        color: #4caf50;
    }
</style>

<div id="code-workspace" class="code-workspace">
    <div class="workspace-header">
        <div class="workspace-title">
            <ion-icon name="code-slash-outline"></ion-icon> SkillUp Workspace
        </div>
        <div class="workspace-actions">
            <button id="close-workspace-btn" class="workspace-btn">
                <ion-icon name="close-outline"></ion-icon> Exit Workspace
            </button>
        </div>
    </div>
    <div class="workspace-grid" id="workspace-grid">
        <div class="workspace-video-area" id="workspace-video-container">
            <!-- Video iframe moves here dynamically -->
        </div>
        
        <!-- Vertical Resizer -->
        <div class="v-resizer" id="v-resizer"></div>
        
        <div class="workspace-editor-area" id="workspace-editor-area">
            <div id="monaco-editor-container" style="width: 100%; height: 100%;"></div>
        </div>
        
        <!-- Horizontal Resizer -->
        <div class="h-resizer" id="h-resizer"></div>
        
        <div class="workspace-console-area">
            <div class="console-header">
                <span>Output / Console</span>
                <button class="clear-console-btn" id="clear-console-btn" title="Clear Console">
                    <ion-icon name="trash-outline"></ion-icon>
                </button>
            </div>
            <div class="console-body" id="console-output">
                <div class="console-line success">System: Workspace initialized. Ready to code.</div>
                <div class="console-line">>> Type your code above and watch this space.</div>
            </div>
        </div>
    </div>
</div>

<!-- Load Monaco Editor via CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.43.0/min/vs/loader.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Initialize Monaco
        require.config({ paths: { 'vs': 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.43.0/min/vs' }});
        require(['vs/editor/editor.main'], function() {
            window.monacoEditor = monaco.editor.create(document.getElementById('monaco-editor-container'), {
                value: [
                    'function helloWorld() {',
                    '\tconsole.log("Welcome to SkillUp Workspace!");',
                    '}',
                    '',
                    'helloWorld();'
                ].join('\n'),
                language: 'javascript',
                theme: 'vs-dark',
                automaticLayout: true,
                minimap: { enabled: false },
                fontSize: 14,
                fontFamily: "'Fira Code', 'Courier New', monospace"
            });
            
            // Re-layout when container size changes
            const editorArea = document.getElementById('workspace-editor-area');
            if (window.ResizeObserver) {
                const ro = new ResizeObserver(() => {
                    if(window.monacoEditor) {
                        window.monacoEditor.layout();
                    }
                });
                ro.observe(editorArea);
            }
        });

        // Toggle logic
        const workspaceBtn = document.getElementById('open-workspace-btn');
        const closeWorkspaceBtn = document.getElementById('close-workspace-btn');
        const workspaceEl = document.getElementById('code-workspace');
        const originalVideoContainer = document.querySelector('.video-box');
        const workspaceVideoContainer = document.getElementById('workspace-video-container');

        if(workspaceBtn) {
            workspaceBtn.addEventListener('click', () => {
                // Move video to workspace
                const playerEl = document.getElementById('player');
                if(playerEl) {
                    workspaceVideoContainer.appendChild(playerEl);
                }
                
                workspaceEl.classList.add('active');
                document.body.style.overflow = 'hidden'; // prevent background scrolling
                
                // Force layout update on open
                if(window.monacoEditor) {
                    setTimeout(() => window.monacoEditor.layout(), 100);
                }
            });
        }

        if(closeWorkspaceBtn) {
            closeWorkspaceBtn.addEventListener('click', () => {
                // Move video back to original location
                const playerEl = document.getElementById('player');
                if(playerEl) {
                    originalVideoContainer.appendChild(playerEl);
                }

                workspaceEl.classList.remove('active');
                document.body.style.overflow = ''; // restore scrolling
            });
        }
        
        // Clear console mock functionality
        document.getElementById('clear-console-btn')?.addEventListener('click', () => {
            const consoleOutput = document.getElementById('console-output');
            if(consoleOutput) {
                consoleOutput.innerHTML = '<div class="console-line">>> Console cleared.</div>';
            }
        });

        // Resizer Logic
        const grid = document.getElementById('workspace-grid');
        const vResizer = document.getElementById('v-resizer');
        const hResizer = document.getElementById('h-resizer');

        let isResizingV = false;
        let isResizingH = false;

        vResizer.addEventListener('mousedown', (e) => {
            isResizingV = true;
            grid.classList.add('is-dragging');
            vResizer.classList.add('dragging');
            e.preventDefault();
        });

        hResizer.addEventListener('mousedown', (e) => {
            isResizingH = true;
            grid.classList.add('is-dragging');
            hResizer.classList.add('dragging');
            e.preventDefault();
        });

        document.addEventListener('mousemove', (e) => {
            if (!isResizingV && !isResizingH) return;
            
            if (isResizingV) {
                const newWidth = (e.clientX / window.innerWidth) * 100;
                if (newWidth > 15 && newWidth < 85) { // min/max bounds
                    grid.style.setProperty('--video-width', `calc(${newWidth}% - 2px)`);
                }
            }
            
            if (isResizingH) {
                const headerHeight = 50; // .workspace-header height
                const gridHeight = window.innerHeight - headerHeight;
                const newHeight = ((window.innerHeight - e.clientY) / gridHeight) * 100;
                
                if (newHeight > 10 && newHeight < 80) { // min/max bounds
                    grid.style.setProperty('--console-height', `calc(${newHeight}% - 2px)`);
                }
            }
        });

        document.addEventListener('mouseup', () => {
            if (isResizingV || isResizingH) {
                isResizingV = false;
                isResizingH = false;
                grid.classList.remove('is-dragging');
                vResizer.classList.remove('dragging');
                hResizer.classList.remove('dragging');
            }
        });
    });
</script>
