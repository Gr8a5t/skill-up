import React from 'react';

declare global {
  interface Window {
    YT: any;
    onYouTubeIframeAPIReady?: () => void;
  }

  namespace JSX {
    interface IntrinsicElements {
      'ion-icon': any;
    }
  }
}

declare module 'react' {
  namespace JSX {
    interface IntrinsicElements {
      'ion-icon': any;
    }
  }
}
