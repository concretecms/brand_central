import React, { Component } from 'react'
import Asset from './asset'
import BulkUpload from './bulkUpload'

export default class App extends Component{
    render() {
        return(
            this.props.isBulkUpload ? <BulkUpload /> : <Asset assetId={this.props.assetId}/>
        )
    }
}