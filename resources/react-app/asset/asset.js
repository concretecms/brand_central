import React from 'react'
import ReactDOM from 'react-dom'
import {Provider} from 'react-redux'
import $ from 'jquery'

import App from './containers/app'
import store from './store'



$(document).ready(() => {

  const container = document.getElementById('asset')

  ReactDOM.render(
    <Provider store={store}>
       <App 
          assetId={container.dataset.asset} 
          isBulkUpload={container.dataset.bulkupload} /> 
    </Provider>, 
    document.getElementById('asset'));
});

