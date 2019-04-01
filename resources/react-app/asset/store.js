import {createStore, combineReducers, applyMiddleware} from 'redux'
import asset from './reducers/assetReducer'
import app from './reducers/appReducer'
import bulkUpload from './reducers/bulkUploadReducer'
import thunk from 'redux-thunk'


export default createStore(
    combineReducers({
        app, asset, bulkUpload 
    }),{},
    applyMiddleware(thunk)
)