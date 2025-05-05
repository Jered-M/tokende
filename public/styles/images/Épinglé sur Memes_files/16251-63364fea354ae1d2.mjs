"use strict";(self.__LOADABLE_LOADED_CHUNKS__=self.__LOADABLE_LOADED_CHUNKS__||[]).push([[16251],{720687:(e,t,n)=>{n.d(t,{default:()=>r});var i=n(934980);let a=`pulsing {
  0% {
    opacity: 1;
  }

  50% {
    opacity: 0.4;
  }

  100% {
    opacity: 1;
  }
}`,r={css:(0,i.Ll)([a]),animation:"pulsing 2s infinite"}},934980:(e,t,n)=>{n.d(t,{CC:()=>a,Ll:()=>o,XF:()=>r});let i=(e,t,n)=>({x:Math.floor(e*Math.cos(n)),y:Math.floor(t*Math.sin(n))}),a=(e,t)=>i(t/2,e/2,2*Math.random()*Math.PI),r=(e,t)=>Math.floor(Math.random()*(t-e+1))+e,o=e=>["@-webkit-keyframes","@keyframes"].map(t=>e.map(e=>t+" "+e).join("\n")).join("\n")},31723:(e,t,n)=>{n.r(t),n.d(t,{default:()=>j});var i=n(667294),a=n(883119),r=n(573706),o=n(986782);function s(e,t,n){var i;return(t="symbol"==typeof(i=function(e,t){if("object"!=typeof e||!e)return e;var n=e[Symbol.toPrimitive];if(void 0!==n){var i=n.call(e,t||"default");if("object"!=typeof i)return i;throw TypeError("@@toPrimitive must return a primitive value.")}return("string"===t?String:Number)(e)}(t,"string"))?i:i+"")in e?Object.defineProperty(e,t,{value:n,enumerable:!0,configurable:!0,writable:!0}):e[t]=n,e}let l={},m=e=>{let t=e.__id||e.id;return"string"==typeof t&&t||null};class u{constructor(){s(this,"idMap",new Map),s(this,"objMap",new WeakMap)}get(e){let t=m(e);return this.objMap.get(e)??(t?this.idMap.get(t):void 0)}has(e){let t=m(e);return this.objMap.has(e)??(!!t&&this.idMap.has(t))}set(e,t){let n=m(e);n&&this.idMap.set(n,t),this.objMap.set(e,t)}reset(){this.idMap=new Map,this.objMap=new WeakMap}}function p(e,t){return"number"==typeof e?e:"_lg1"===t?e[t]??e.lg??1:e[t]??1}var d=n(587435),c=n(39260),h=n(876594),g=n(720687),_=n(512541),f=n(785893);let{css:y,animation:b}=g.default,x={backgroundColor:h._VP,animation:b,borderRadius:h.Ev2};function w({data:e}){let{height:t}=e;return(0,f.jsxs)(i.Fragment,{children:[(0,f.jsx)(_.Z,{unsafeCSS:y}),(0,f.jsx)(a.xu,{dangerouslySetInlineStyle:{__style:x},"data-test-id":"skeleton-pin",children:(0,f.jsx)(a.xu,{height:t})})]})}var v=n(679482),M=n(297728),C=n(730212),S=n(410150),$=n(415787),k=n(855746);function j(e){let t,{align:n,cacheKey:s,id:m,isFetching:h,isGridCentered:g=!0,items:y,layout:b,loadItems:x,masonryRef:j,optOutFluidGridExperiment:E=!1,renderItem:R,scrollContainerRef:I,virtualize:W=!0,_getColumnSpanConfig:A,_getResponsiveModuleConfigForSecondItem:B,_dynamicHeights:P,useLoadingState:G,initialLoadingStatePinCount:F,isLoadingAccessibilityLabel:O,isLoadedAccessibilityLabel:z}=e,N=(0,S.ZP)(),{isAuthenticated:L,isRTL:T}=(0,C.B)(),{logContextEvent:X}=(0,r.v)(),D=(0,M.F)(),Z="desktop"===N,H=(0,k.MM)(),Q=((0,i.useRef)(y.map(()=>({fetchTimestamp:Date.now(),measureTimestamp:Date.now(),hasRendered:!1,pageCount:0}))),Z&&!E),{experimentalColumnWidth:V,experimentalGutter:q}=(0,d.Z)(Q),K=e.serverRender??!!Z,U="flexible"===b||"uniformRowFlexible"===b||"desktop"!==N||Q,J=(U&&b?.startsWith("uniformRow")?"uniformRowFlexible":void 0)??(K?"serverRenderedFlexible":"flexible"),Y=e.columnWidth??V??v.yF;U&&(Y=Math.floor(Y));let ee=e.gutterWidth??q??(Z?v.oX:1),et=e.minCols??v.yc,en=(0,i.useRef)(0),ei=Y+ee,ea=function(e){if(null==e)return;let t=function(e){let t=l[e];return t&&t.screenWidth===window.innerWidth||(l[e]={screenWidth:window.innerWidth}),l[e]}(e);return t.measurementCache||(t.measurementCache=new u),t.measurementCache}(s),er=(0,i.useCallback)(()=>I?.current||window,[I]),eo=(0,i.useRef)(!0),{anyEnabled:es}=D.checkExperiment("web_masonry_enable_dynamic_heights_for_all"),{anyEnabled:el}=D.checkExperiment("web_masonry_pin_overlap_calculation_and_logging"),{anyEnabled:em,group:eu}=D.checkExperiment("web_masonry_dynamic_batches");em&&(t=(e,t)=>{let n={itemsBatchSize:0,whitespaceThreshold:0,iterationsLimit:15e3};return t>3&&(n.whitespaceThreshold=ee*t),"enabled_sections_position_fixed"===eu&&(n.itemsBatchSize=5,n.iterationsLimit=1e4),"enabled_sections_position_responsive"===eu&&(n.iterationsLimit=15e3,n.itemsBatchSize=5,t>5&&(n.itemsBatchSize=7)),("enabled_small_batch"===eu||"employees"===eu)&&(e>=7?n.itemsBatchSize=7:n.itemsBatchSize=5),"enabled_large_batch"===eu&&(e>=7?n.itemsBatchSize=9:n.itemsBatchSize=5),n});let ep=g&&eo.current?"centered":"",{className:ed,styles:ec}=function(e){let t=`m_${Object.keys(e).sort().reduce((t,n)=>{let i=e[n];return null==i||"object"==typeof i||"function"==typeof i?t:"boolean"==typeof i?t+(i?"t":"f"):t+i},"").replace(/\:/g,"\\:")}`,{flexible:n,gutterWidth:i,isRTL:a,itemWidth:r,maxColumns:o,minColumns:s,items:l,_getColumnSpanConfig:m,_getResponsiveModuleConfigForSecondItem:u}=e,d=m?l.map((e,t)=>({index:t,columnSpanConfig:m(e)??1})).filter(e=>1!==e.columnSpanConfig):[],c=r+i,h=Array.from({length:o+1-s},(e,t)=>t+s).map(e=>{let h,g,_=e===s?0:e*c,f=e===o?null:(e+1)*c-.01;m&&u&&l.length>1&&(h=m(l[0]),g=u(l[1]));let{styles:y,numberOfVisibleItems:b}=d.reduce((a,o)=>{let{columnSpanConfig:s}=o,m=Math.min(function({columnCount:e,columnSpanConfig:t,firstItemColumnSpanConfig:n,isFlexibleWidthItem:i,secondItemResponsiveModuleConfig:a}){let r=e<=2?"sm":e<=4?"md":e<=6?"_lg1":e<=8?"lg":"xl",o=p(t,r);if(i){let t=p(n,r);o="number"==typeof a?a:a?Math.max(a.min,Math.min(a.max,e-t)):1}return o}({columnCount:e,columnSpanConfig:s,isFlexibleWidthItem:!!g&&o===l[1],firstItemColumnSpanConfig:h??1,secondItemResponsiveModuleConfig:g??1}),e),u=null!=o.index&&a.numberOfVisibleItems>=m+o.index,d=n?100/e*m:r*m+i*(m-1),{numberOfVisibleItems:c}=a;return u?c-=m-1:o.index<c&&(c+=1),{styles:a.styles.concat(function({className:e,index:t,columnSpanConfig:n,visible:i,width:a,flexible:r}){let o="number"==typeof n?n:btoa(JSON.stringify(n));return r?`
      .${e} .static[data-column-span="${o}"]:nth-child(${t+1}) {
        visibility: ${i?"visible":"hidden"} !important;
        position: ${i?"inherit":"absolute"} !important;
        width: ${a}% !important;
      }`:`
      .${e} .static[data-column-span="${o}"]:nth-child(${t+1}) {
        visibility: ${i?"visible":"hidden"} !important;
        position: ${i?"inherit":"absolute"} !important;
        width: ${a}px !important;
      }`}({className:t,index:o.index,columnSpanConfig:s,visible:u,width:d,flexible:n})),numberOfVisibleItems:c}},{styles:"",numberOfVisibleItems:e}),x=n?`
      .${t} .static {
        box-sizing: border-box;
        width: calc(100% / ${e}) !important;
      }
    `:`
      .${t} {
        max-width: ${e*c}px;
      }

      .${t} .static {
        width: ${r}px !important;
      }
    `;return{minWidth:_,maxWidth:f,styles:`
      .${t} .static:nth-child(-n+${b}) {
        position: static !important;
        visibility: visible !important;
        float: ${a?"right":"left"};
        display: block;
      }

      .${t} .static {
        padding: 0 ${i/2}px;
      }

      ${x}

      ${y}
    `}}),g=h.map(({minWidth:e,maxWidth:t,styles:n})=>`
    @container (min-width: ${e}px) ${t?`and (max-width: ${t}px)`:""} {
      ${n}
    }
  `),_=h.map(({minWidth:e,maxWidth:t,styles:n})=>`
    @media (min-width: ${e}px) ${t?`and (max-width: ${t}px)`:""} {
      ${n}
    }
  `),f=`
    ${g.join("")}
    @supports not (container-type: inline-size) {
      ${_.join("")}
    }
  `;return{className:t,styles:`
    .masonryContainer:has(.${t}) {
      container-type: inline-size;
    }

    .masonryContainer > .centered {
      margin-left: auto;
      margin-right: auto;
    }

    .${t} .static {
      position: absolute !important;
      visibility: hidden !important;
    }

    ${f}
  `}}({gutterWidth:ee,flexible:U,items:y,isRTL:T,itemWidth:Y,maxColumns:e.maxColumns??Math.max(y.length,v.g5),minColumns:et,_getColumnSpanConfig:A,_getResponsiveModuleConfigForSecondItem:B}),eh=`${ep} ${ed}`.trim(),{anyEnabled:eg,expName:e_,group:ef,isMeasureAllEnabled:ey}=(0,c.Z)(),eb=((0,i.useRef)(),(0,i.useRef)(y.length)),ex=(0,i.useRef)(0),ew=(0,i.useRef)(null);(0,i.useEffect)(()=>{eb.current=y.length,ex.current+=1},[y]),(0,i.useEffect)(()=>{eo.current&&(eo.current=!1)},[]),(0,i.useEffect)(()=>()=>{},[]);let ev=(0,i.useCallback)((e,t,n)=>{let i=e.reduce((e,t)=>e+t),a=i/e.length;(0,$.S0)("webapp.masonry.multiColumnWhitespace.average",a,{sampleRate:1,tags:{experimentalMasonryGroup:ef||"unknown",dynamicBatchesExperimentGroup:eu||"unknown",handlerId:H,isAuthenticated:L,multiColumnItemSpan:e.length}}),(0,$.S0)("webapp.masonry.twoColWhitespace",a,{sampleRate:1,tags:{columnWidth:Y,minCols:et}}),eu&&(0,$.S0)("webapp.masonry.graphIterations",t,{sampleRate:1,tags:{columnSpan:n,experimentGroup:eu}}),X({event_type:15878,component:14468,aux_data:{total_whitespace_px:i}}),X({event_type:16062,component:14468,aux_data:{average_whitespace_px:a}}),X({event_type:16063,component:14468,aux_data:{max_whitespace_px:Math.max(...e)}}),e.forEach(t=>{t>=50&&((0,$.nP)("webapp.masonry.multiColumnWhitespace.over50",{sampleRate:1,tags:{experimentalMasonryGroup:ef||"unknown",dynamicBatchesExperimentGroup:eu||"unknown",handlerId:H,isAuthenticated:L,multiColumnItemSpan:e.length}}),X({event_type:16261,component:14468})),t>=100&&((0,$.nP)("webapp.masonry.multiColumnWhitespace.over100",{sampleRate:1,tags:{experimentalMasonryGroup:ef||"unknown",dynamicBatchesExperimentGroup:eu||"unknown",handlerId:H,isAuthenticated:L,multiColumnItemSpan:e.length}}),X({event_type:16262,component:14468}))}),(0,$.nP)("webapp.masonry.multiColumnWhitespace.count",{sampleRate:1,tags:{experimentalMasonryGroup:ef||"unknown",dynamicBatchesExperimentGroup:eu||"unknown",handlerId:H,isAuthenticated:L,multiColumnItemSpan:e.length}})},[Y,X,et,L,H,ef,eu]),{_items:eM,_renderItem:eC}=function({initialLoadingStatePinCount:e=50,infiniteScrollPinCount:t=10,isFetching:n,items:a=[],renderItem:r,useLoadingState:o}){let s=a.filter(e=>"object"==typeof e&&null!==e&&"type"in e&&"closeup_module"===e.type).length>0,l=o&&n&&0===a.length,m=o&&n&&s&&1===a.length,u=o&&n&&a.length>+!!s,p=(0,i.useMemo)(()=>Array.from({length:u?t:e}).reduce((e,t,n)=>[...e,{height:n%2==0?356:236,key:`skeleton-pin-${n}`,isSkeleton:!0}],[]),[e,t,u]);return{_items:(0,i.useMemo)(()=>m||u?[...a,...p]:l?p:a,[l,u,m,a,p]),_renderItem:(0,i.useMemo)(()=>o?e=>{let{itemIdx:t,data:n}=e;return t>=a.length&&n&&"object"==typeof n&&"key"in n&&"height"in n?(0,f.jsx)(w,{data:n},n.key):r(e)}:r,[o,r,a.length])}}({useLoadingState:G,items:y,renderItem:(0,i.useCallback)(e=>(0,f.jsx)(o.Z,{name:"MasonryItem",children:R(e)}),[R]),isFetching:h,initialLoadingStatePinCount:F}),eS=G&&h,e$=(0,i.useRef)(new Set);return(0,i.useEffect)(()=>{if(!el)return;let e=setTimeout(()=>{requestAnimationFrame(()=>{let e=Array.from(ew.current?.querySelectorAll("[data-grid-item-idx]")??[]);if(0===e.length)return;let t=e.map(e=>{let t=e.getAttribute("data-grid-item-idx");return{rect:e.getBoundingClientRect(),itemIdx:t}}),n=0,i=0,a=0,r=0,o=0,s=0;for(let e=0;e<t.length;e+=1){let l=t[e]?.rect,m=t[e]?.itemIdx;for(let u=e+1;u<t.length;u+=1){let e=t[u]?.rect,p=t[u]?.itemIdx;if(l&&e&&m&&p){let t=[m,p].sort().join("|");if(!e$.current.has(t)&&l.right>=e.left&&l.left<=e.right&&l.bottom>=e.top&&l.top<=e.bottom&&l.height>0&&e.height>0){e$.current.add(t),n+=1;let m=Math.max(0,Math.min(l.right,e.right)-Math.max(l.left,e.left))*Math.max(0,Math.min(l.bottom,e.bottom)-Math.max(l.top,e.top));m>8e4?s+=1:m>4e4?o+=1:m>1e4?r+=1:m>5e3?a+=1:m>2500&&(i+=1)}}}}n>0&&(0,$.QX)("webapp.masonry.pinOverlapHits",n,{tags:{isAuthenticated:L,isDesktop:Z,experimentalMasonryGroup:ef||"unknown"}}),i>0&&(0,$.QX)("webapp.masonry.pinOverlap.AreaPx.over2500",i,{tags:{isAuthenticated:L,isDesktop:Z,experimentalMasonryGroup:ef||"unknown"}}),a>0&&(0,$.QX)("webapp.masonry.pinOverlap.AreaPx.over5000",a,{tags:{isAuthenticated:L,isDesktop:Z,experimentalMasonryGroup:ef||"unknown"}}),r>0&&(0,$.QX)("webapp.masonry.pinOverlap.AreaPx.over10000",r,{tags:{isAuthenticated:L,isDesktop:Z,experimentalMasonryGroup:ef||"unknown"}}),o>0&&(0,$.QX)("webapp.masonry.pinOverlap.AreaPx.over40000",o,{tags:{isAuthenticated:L,isDesktop:Z,experimentalMasonryGroup:ef||"unknown"}}),s>0&&(0,$.QX)("webapp.masonry.pinOverlap.AreaPx.over80000",s,{tags:{isAuthenticated:L,isDesktop:Z,experimentalMasonryGroup:ef||"unknown"}})})},1e3);return()=>{clearTimeout(e)}},[Y,ef,L,Z,el,y]),(0,f.jsxs)(i.Fragment,{children:[G&&!eo.current&&(0,f.jsx)(a.xu,{"aria-live":"polite",display:"visuallyHidden",children:eS?O:z}),(0,f.jsx)("div",{ref:ew,"aria-busy":G?!!eS:void 0,className:"masonryContainer","data-test-id":"masonry-container",id:m,style:Q?{padding:`0 ${ee/2}px`}:void 0,children:(0,f.jsxs)("div",{className:eh,children:[K&&eo.current?(0,f.jsx)(_.Z,{"data-test-id":"masonry-ssr-styles",unsafeCSS:ec}):null,(0,f.jsx)(a.xu,{"data-test-id":"max-width-container",marginBottom:0,marginEnd:"auto",marginStart:"auto",marginTop:0,maxWidth:e.maxColumns?ei*e.maxColumns:void 0,children:eg?(0,f.jsx)(a.GX,{ref:e=>{j&&(j.current=e)},_dynamicHeights:es||P,_enableSectioningPosition:em&&eu.includes("enabled_sections_position"),_getColumnSpanConfig:A,_getModulePositioningConfig:t,_getResponsiveModuleConfigForSecondItem:B,_logTwoColWhitespace:ev,_measureAll:ey,align:n,columnWidth:Y,gutterWidth:ee,items:eM,layout:U?J:b??"basic",loadItems:x,measurementStore:ea,minCols:et,renderItem:eC,scrollContainer:er,virtualBufferFactor:.3,virtualize:W}):(0,f.jsx)(a.Rk,{ref:e=>{j&&(j.current=e)},_dynamicHeights:es||P,_enableSectioningPosition:em&&eu.includes("enabled_sections_position"),_getColumnSpanConfig:A,_getModulePositioningConfig:t,_getResponsiveModuleConfigForSecondItem:B,_logTwoColWhitespace:ev,align:n,columnWidth:Y,gutterWidth:ee,items:eM,layout:U?J:b??"basic",loadItems:x,measurementStore:ea,minCols:et,renderItem:eC,scrollContainer:er,virtualBufferFactor:.3,virtualize:W})})]})})]})}},587435:(e,t,n)=>{n.d(t,{Z:()=>i});function i(e=!0){let t=e?16:void 0,n=t?Math.floor(t/4):void 0;return{experimentalColumnWidth:e?221:void 0,experimentalGutter:t,experimentalGutterBoints:n}}},39260:(e,t,n)=>{n.d(t,{Z:()=>o});var i=n(297728),a=n(730212),r=n(855746);function o(e){let{isAuthenticated:t}=(0,a.B)(),{expName:n,anyEnabled:o,group:s}=function({experimentsClient:e,handlerId:t,isAuthenticated:n,skipActivation:i}){let{checkExperiment:a}=e,r=a(n?"web_masonry_v2_auth":"web_masonry_v2_unauth",{dangerouslySkipActivation:i});return r.group?{expName:n?"web_masonry_v2_auth":"web_masonry_v2_unauth",...r}:"www/[username]/[slug].js"!==t||n?"www/pin/[id].js"!==t||n?{expName:"",anyEnabled:!1,group:""}:{expName:"web_masonry_v2_unauth_pin",...a("web_masonry_v2_unauth_pin",{dangerouslySkipActivation:i})}:{expName:"web_masonry_v2_unauth_board",...a("web_masonry_v2_unauth_board",{dangerouslySkipActivation:i})}}({experimentsClient:(0,i.F)(),handlerId:(0,r.MM)(),isAuthenticated:t,skipActivation:e?.skipActivation??!1});return{expName:n,anyEnabled:o,group:s,isMeasureAllEnabled:"enabled_measure_all"===s||"enabled_measure_all_impression_fix"===s,isImpressionFixEnabled:"control_impression_fix"===s||"enabled_impression_fix"===s||"enabled_measure_all_impression_fix"===s}}}}]);
//# sourceMappingURL=https://sm.pinimg.com/webapp/16251-63364fea354ae1d2.mjs.map