(()=>{"use strict";const e=window.React,t=JSON.parse('{"$schema":"https://json.schemastore.org/block.json","apiVersion":2,"name":"igd/shortcodes","version":"1.0.0","title":"Module Shortcodes","category":"igd-category","icon":"shortcode","description":"Insert pre-built module\'s shortcode","supports":{"html":false},"attributes":{"id":{"type":"number","default":0}},"keywords":["google drive","drive","shortcode","module","cloud","shortcode"],"textdomain":"igd","editorScript":"file:./index.js","editorStyle":"file:./index.css"}'),{PanelBody:l,PanelRow:o,SelectControl:n}=wp.components,{InspectorControls:r}=wp.blockEditor;function i({attributes:t,setAttributes:i,options:a}){const{id:c}=t;return(0,e.createElement)(r,null,(0,e.createElement)(l,{title:"Settings",icon:"dashicons-shortcode",initialOpen:!0},(0,e.createElement)(o,null,(0,e.createElement)(n,{label:"Select shortcode: ",options:a,value:c,onChange:e=>{i({id:parseInt(e)})},help:"Select a module shortcode to insert the module."}))))}const a=window.wp.blockEditor,c=window.wp.components,s=window.wp.element,{registerBlockType:d}=wp.blocks;d("igd/shortcodes",{...t,icon:(0,e.createElement)("svg",{width:"22",height:"26",viewBox:"0 0 22 26",fill:"none",xmlns:"http://www.w3.org/2000/svg"},(0,e.createElement)("g",{"clip-path":"url(#clip0_932_7536)"},(0,e.createElement)("path",{d:"M6.57378 22.4851C4.55587 22.4851 2.61411 22.4851 0.615234 22.4851C0.615234 16.1683 0.615234 9.87104 0.615234 3.53467C2.59507 3.53467 4.53683 3.53467 6.57378 3.53467C6.57378 4.5125 6.57378 5.5099 6.57378 6.58552C5.64097 6.58552 4.67009 6.58552 3.6421 6.58552C3.6421 10.8684 3.6421 15.0536 3.6421 19.3365C4.61298 19.3365 5.58386 19.3365 6.57378 19.3365C6.57378 20.4512 6.57378 21.4486 6.57378 22.4851Z",fill:"url(#paint0_linear_932_7536)"}),(0,e.createElement)("path",{d:"M21.3843 22.4655C19.3664 22.4655 17.4246 22.4655 15.3877 22.4655C15.3877 21.4486 15.3877 20.4512 15.3877 19.3756C16.3586 19.3756 17.3295 19.3756 18.3384 19.3756C18.3384 15.0926 18.3384 10.9075 18.3384 6.66369C17.3485 6.66369 16.4157 6.66369 15.3877 6.66369C15.3877 5.60763 15.3877 4.62979 15.3877 3.57373C17.3485 3.57373 19.3283 3.57373 21.3653 3.57373C21.3843 9.81232 21.3843 16.09 21.3843 22.4655Z",fill:"url(#paint1_linear_932_7536)"}),(0,e.createElement)("path",{d:"M11.3138 24.8708L7.65869 25.6922L10.4571 1.36368L14.1312 0.307617L11.3138 24.8708Z",fill:"url(#paint2_linear_932_7536)"})),(0,e.createElement)("defs",null,(0,e.createElement)("linearGradient",{id:"paint0_linear_932_7536",x1:"3.59451",y1:"3.53467",x2:"3.59451",y2:"22.4851",gradientUnits:"userSpaceOnUse"},(0,e.createElement)("stop",{stopColor:"#50D890"}),(0,e.createElement)("stop",{offset:"1",stopColor:"#28CAA3"})),(0,e.createElement)("linearGradient",{id:"paint1_linear_932_7536",x1:"18.386",y1:"3.57373",x2:"18.386",y2:"22.4655",gradientUnits:"userSpaceOnUse"},(0,e.createElement)("stop",{stopColor:"#50D890"}),(0,e.createElement)("stop",{offset:"1",stopColor:"#28CAA3"})),(0,e.createElement)("linearGradient",{id:"paint2_linear_932_7536",x1:"10.895",y1:"0.307617",x2:"10.895",y2:"25.6922",gradientUnits:"userSpaceOnUse"},(0,e.createElement)("stop",{stopColor:"#50D890"}),(0,e.createElement)("stop",{offset:"1",stopColor:"#28CAA3"})),(0,e.createElement)("clipPath",{id:"clip0_932_7536"},(0,e.createElement)("rect",{width:"20.7692",height:"25.3846",fill:"white",transform:"translate(0.615234 0.307617)"})))),edit:function({attributes:t,setAttributes:l}){const{IgdShortcode:o}=window,n=(0,e.createElement)("svg",{width:20,height:20,viewBox:"0 0 87.3 78",xmlns:"http://www.w3.org/2000/svg"},(0,e.createElement)("path",{d:"m6.6 66.85 3.85 6.65c.8 1.4 1.95 2.5 3.3 3.3l13.75-23.8h-27.5c0 1.55.4 3.1 1.2 4.5z",fill:"#0066da"}),(0,e.createElement)("path",{d:"m43.65 25-13.75-23.8c-1.35.8-2.5 1.9-3.3 3.3l-25.4 44a9.06 9.06 0 0 0 -1.2 4.5h27.5z",fill:"#00ac47"}),(0,e.createElement)("path",{d:"m73.55 76.8c1.35-.8 2.5-1.9 3.3-3.3l1.6-2.75 7.65-13.25c.8-1.4 1.2-2.95 1.2-4.5h-27.502l5.852 11.5z",fill:"#ea4335"}),(0,e.createElement)("path",{d:"m43.65 25 13.75-23.8c-1.35-.8-2.9-1.2-4.5-1.2h-18.5c-1.6 0-3.15.45-4.5 1.2z",fill:"#00832d"}),(0,e.createElement)("path",{d:"m59.8 53h-32.3l-13.75 23.8c1.35.8 2.9 1.2 4.5 1.2h50.8c1.6 0 3.15-.45 4.5-1.2z",fill:"#2684fc"}),(0,e.createElement)("path",{d:"m73.4 26.5-12.7-22c-.8-1.4-1.95-2.5-3.3-3.3l-13.75 23.8 16.15 28h27.45c0-1.55-.4-3.1-1.2-4.5z",fill:"#ffba00"})),[r,d]=(0,s.useState)(!0),[p,m]=(0,s.useState)([]),{id:h}=t;(0,s.useEffect)((()=>{wp.ajax.post("igd_get_shortcodes",{nonce:igd.nonce}).done((({shortcodes:e})=>{d(!1),m(e)}))}),[]);const g=!!p&&[{label:wp.i18n.__("Select shortcode","integrate-google-drive"),value:""},...p.map((e=>({label:e.title,value:e.id})))];return(0,e.createElement)("div",{...(0,a.useBlockProps)()},(0,e.createElement)(i,{attributes:t,setAttributes:l,options:g}),!!h&&(0,e.createElement)(a.BlockControls,null,(0,e.createElement)(c.ToolbarButton,{icon:"edit",text:wp.i18n.__("Change Shortcode","integrate-google-drive"),label:wp.i18n.__("Change Shortcode","integrate-google-drive"),onClick:e=>l({id:null}),showTooltip:!0})),!h&&(0,e.createElement)(c.Placeholder,{icon:n,label:wp.i18n.__("Module Shortcodes","integrate-google-drive"),instructions:wp.i18n.__("Select a module shortcode to insert.","integrate-google-drive"),isColumnLayout:!0},r?(0,e.createElement)(c.Spinner,null):(0,e.createElement)(e.Fragment,null,(0,e.createElement)("p",null,"Select shortcode:"),(0,e.createElement)(c.SelectControl,{options:g,value:h,onChange:e=>{l({id:parseInt(e)})},style:{minHeight:"40px"}}))),!!h&&!!p.length&&(0,e.createElement)(o,{data:p.find((e=>e.id==h)).config,isPreview:!0}))}})})();