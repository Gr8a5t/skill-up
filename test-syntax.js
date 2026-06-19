
    // 1. Dynamic Monaco Initialization
    function initMonaco() {
        if (window._workspaceMonacoInitStarted) return;
        window._workspaceMonacoInitStarted = true;

        const script = document.createElement('script');
        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.43.0/min/vs/loader.min.js';
        script.onload = () => {
            require.config({ paths: { 'vs': 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.43.0/min/vs' }});
            require(['vs/editor/editor.main'], function() {
                const container = document.getElementById('monaco-editor-container');
                if(!container) return;

                window.monacoEditor = monaco.editor.create(container, {
                    value: [
                        '<html>',
                        '    <head>',
                        '        <link rel="stylesheet" href="index.css">',
                        '    </head>',
                        '    <body>',
                        '        <h1>People entered:</h1>',
                        '        <h2 id="count-el">0</h2>',
                        '        ',
                        '            document.getElementById("count-el").innerText = 5;',
                        '        </sc' + 'ript>',
                        '    </body>',
                        '</html>'
                    ].join('\n'),
                    language: 'html',
                    theme: 'vs-dark',
                    automaticLayout: true,
                    minimap: { enabled: false },
                    fontSize: 14,
                    fontFamily: "'Fira Code', 'Courier New', monospace"
                });
                
                if (window.ResizeObserver) {
                    const ro = new ResizeObserver(() => {
                        if(window.monacoEditor) {
                            window.monacoEditor.layout();
                        }
                    });
                    ro.observe(container);
                }
            });
        };
        document.head.appendChild(script);
    }

    // 2. Global UI Functions (resilient to Livewire DOM updates)
    window.openSkillUpWorkspace = function() {
        const workspaceEl = document.getElementById('code-workspace');
        const playerEl = document.getElementById('player');
        const workspaceVideoContainer = document.getElementById('workspace-video-container');

        if(playerEl && workspaceVideoContainer) {
            workspaceVideoContainer.appendChild(playerEl);
        }
        
        if(workspaceEl) workspaceEl.classList.add('active');
        document.body.style.overflow = 'hidden'; 
        
        if (!window.monacoEditor) {
            initMonaco();
        } else {
            setTimeout(() => window.monacoEditor.layout(), 100);
        }
    };

    window.closeSkillUpWorkspace = function() {
        const workspaceEl = document.getElementById('code-workspace');
        const playerEl = document.getElementById('player');
        const originalVideoContainer = document.querySelector('.video-box');

        if(playerEl && originalVideoContainer) {
            originalVideoContainer.appendChild(playerEl);
        }

        if(workspaceEl) workspaceEl.classList.remove('active');
        document.body.style.overflow = ''; 
    };
    
    window.clearSkillUpConsole = function() {
        const consoleOutput = document.getElementById('console-output');
        if(consoleOutput) {
            consoleOutput.innerHTML = '<div class="console-line"><span class="console-prompt">~/skillup/project$</span></div>';
        }
    };

    // 3. Document-level drag listeners for Resizers
    if (!window._workspaceEventsBound) {
        window._workspaceEventsBound = true;
        let isResizingV = false;
        let isResizingH = false;

        document.addEventListener('mousedown', (e) => {
            const grid = document.getElementById('workspace-grid');
            if(!grid) return;
            
            if (e.target && e.target.id === 'v-resizer') {
                isResizingV = true;
                grid.classList.add('is-dragging');
                e.target.classList.add('dragging');
                e.preventDefault();
            } else if (e.target && e.target.id === 'h-resizer') {
                isResizingH = true;
                grid.classList.add('is-dragging');
                e.target.classList.add('dragging');
                e.preventDefault();
            }
        });

        document.addEventListener('mousemove', (e) => {
            if (!isResizingV && !isResizingH) return;
            const grid = document.getElementById('workspace-grid');
            if(!grid) return;
            
            if (isResizingV) {
                const newWidth = (e.clientX / window.innerWidth) * 100;
                if (newWidth > 15 && newWidth < 85) {
                    grid.style.setProperty('--video-width', `calc(${newWidth}% - 2px)`);
                }
            }
            
            if (isResizingH) {
                const headerHeight = 50; 
                const gridHeight = window.innerHeight - headerHeight;
                const newHeight = ((window.innerHeight - e.clientY) / gridHeight) * 100;
                
                if (newHeight > 10 && newHeight < 80) {
                    grid.style.setProperty('--console-height', `calc(${newHeight}% - 2px)`);
                }
            }
        });

        document.addEventListener('mouseup', () => {
            if (isResizingV || isResizingH) {
                isResizingV = false;
                isResizingH = false;
                const grid = document.getElementById('workspace-grid');
                const vResizer = document.getElementById('v-resizer');
                const hResizer = document.getElementById('h-resizer');
                
                if(grid) grid.classList.remove('is-dragging');
                if(vResizer) vResizer.classList.remove('dragging');
                if(hResizer) hResizer.classList.remove('dragging');
            }
        });
    }

