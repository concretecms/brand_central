import React, { Component } from 'react'
import {connect} from 'react-redux'
import DropdownCollections from '../components/selectCollectionsDropdown'
import BulkFiles from '../containers/bulkUploadFiles'
import CurrentCollections from '../components/currentCollections'
import { runAction } from "../actions/assetActions"
import axios from 'axios';


class BulkUpload extends Component {
    currentCollection (payload) {
        this.props.setCurrentCollection(payload.id)
        this.props.toggleCollectionErr(false)
        this.props.setCollection(payload)
    }

    removeCollection (payload) {
        this.props.rmCollection(payload)
    }

    saveAssets () {

        if(this.props.bulkUpload.collections === undefined || this.props.bulkUpload.collections.length == 0) {
            return this.handleError("COLLECTION_ERROR")
        }

        if(this.props.bulkUpload.assets.length === 0) {
            return this.handleError("MISSING_ASSETS")
        }

        let currentAsset = '';
        this.props.bulkUpload.assets.some( asset => {
            if(asset.name === ''){
                return currentAsset = asset.id
            }
        })

        if(currentAsset){
            return this.handleError("MISSING_ASSET_NAME", currentAsset)
        }

        this.props.processSave(true)

        axios.post(CCM_DISPATCHER_FILENAME + `/api/v1/assets/bulk`, {assets:this.props.bulkUpload.assets, collection:this.props.bulkUpload.currentCollection, collections:this.props.bulkUpload.collections}, {headers :{'X-Requested-With': 'XMLHttpRequest' }})
            .then( response => {
                console.log(this.props.bulkUpload.currentCollection)
                window.location.href = CCM_DISPATCHER_FILENAME + '/collections/' + this.props.bulkUpload.currentCollection
            }).catch(error => {
                this.props.processSave(false)
                this.props.toggleGlobalErr(true)
                this.props.setGlobalErrMsg(error.response.data.errors[0])
            })
    }

    handleError(error, id) {
        switch (error) {
            case "COLLECTION_ERROR" :
                this.props.toggleCollectionErr(true)
                break
            case "MISSING_ASSETS":
                this.props.toggleFilesErr(true)
                this.props.setFilesErrMsg("You need to upload at least one file.")
                break
            case "MISSING_ASSET_NAME":
                this.props.setCurrentAssetErr(id)
                this.props.setEditMode({id:id, isNameEditMode:true})
                break
        }
    }


    render(){
        const collectionError = (
            <span className="alert alert-danger">
                <i className="fa fa-exclamation-triangle"></i> Please Select a <strong>Collection</strong>.
            </span>
        )

        const globalError = (
            <div className="alert alert-danger">
                <i className="fa fa-exclamation-triangle"></i> {this.props.bulkUpload.errorGlobalMsg}
                <button type="button" className="close" onClick={()=>{this.props.toggleGlobalErr(false)}}>
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        )

        return (
            <section>

                {this.props.bulkUpload.isProcessingSave ? <div className="processing-bulk-upload"><span className="loading"></span></div> : null}

                {this.props.bulkUpload.errorGlobal ? globalError : null}


                <div className="asset-app">
                    <div className="row">
                        <div className="col-md-8">
                            <h3>Upload Assets</h3>
                        </div>
                        <div className="col-md-4 text-right">
                            <button className="btn-clear" onClick={()=>{ window.location.href = CCM_DISPATCHER_FILENAME + '/account/welcome' }}>Cancel</button>
                            <button className="btn-bold" onClick={()=> this.saveAssets()}>Save</button>
                        </div>
                    </div>
                    <div className="row">
                        <div className="col-md-6 col-12">
                            <section>
                                <DropdownCollections  label={'Add to Collection'} required={true} onValueChange={(value)=>this.currentCollection(value)} error={this.props.bulkUpload.errorCollection}/>
                            </section>
                        </div>

                        <div className="col-md-6 col-12">
                            <div style={{height:'75px'}}>
                                {this.props.bulkUpload.errorCollection ? collectionError : null}
                            </div>
                        </div>
                    </div>
                    <div className="row">
                        <div className="col-12">
                            <section>
                                <CurrentCollections collections={this.props.bulkUpload.collections } removeItem={(item)=> this.removeCollection(item) } />
                            </section>
                        </div>
                    </div>
                    <div className="row">
                        <div className="col-12">
                            <BulkFiles />
                        </div>
                    </div>
                    <div className="row">
                        <div className="col-md-8">

                        </div>
                        <div className="col-md-4 text-right">
                            <button className="btn-clear" onClick={()=>{ window.location.href = CCM_DISPATCHER_FILENAME + '/account/welcome' }}>Cancel</button>
                            <button className="btn-bold" onClick={()=> this.saveAssets()}>Save</button>
                        </div>
                    </div>
                </div>
            </section>

        )

    }

}
const mapStateToProps = (state) => {
    return {
        bulkUpload:state.bulkUpload,
    }
}

const mapDispatchToProps = (dispatch) => {
    return {
        setCurrentCollection: (payload) => dispatch(runAction("SET_CURRENT_COLLECTION_BULK", payload)),
        processSave: (payload) => dispatch(runAction("SET_IS_PROCESSING_SAVE", payload)),
        toggleCollectionErr: (payload) => dispatch(runAction("SET_COLLECTION_ERROR", payload)),
        toggleFilesErr: (payload) => dispatch(runAction("SET_FILES_ERROR", payload)),
        setFilesErrMsg:(payload) => dispatch(runAction("SET_FILES_ERROR_MSG", payload)),
        setCurrentAssetErr: (payload) => dispatch(runAction("SET_ASSET_ERROR", payload)),
        setEditMode: (payload) => dispatch(runAction("SET_ASSET_BULK_NAME_EDIT_MODE", payload)),
        toggleGlobalErr: (payload) => dispatch(runAction("SET_GLOBAL_ERROR", payload)),
        setGlobalErrMsg: (payload) => dispatch(runAction("SET_GLOBAL_ERROR_MSG",payload)),
        setCollection: (payload) => dispatch(runAction("SET_ASSET_BULK_COLLECTIONS", payload)),
        rmCollection: (payload) => dispatch(runAction("REMOVE_ASSET_BULK_COLLECTION", payload))
    }
}
export default connect(mapStateToProps, mapDispatchToProps)(BulkUpload);
