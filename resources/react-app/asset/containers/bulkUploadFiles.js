import React, {Component} from "react"
import {connect} from "react-redux"
import Dropzone from "react-dropzone"
import { transactionId } from "../lib/assetServices"
import { runAction } from "../actions/assetActions"
import FileTable from "../containers/bulkUploadFileItem"
import axios from "axios"


class BulkFiles extends Component {
    maxFilesErr () {
        this.props.toggleFilesErr(true)
        this.props.setFilesErrMsg(`You can't process more than ${this.props.bulkUpload.maxAssets} files at once.`)
    }

    handleOnDrop (files) {
        this.props.toggleFilesErr(false)
        const currentAssets = this.props.bulkUpload.assets.length + files.length
        if(currentAssets > this.props.bulkUpload.maxAssets){
            return this.maxFilesErr()
        }

        const uploaders = files.map( (file) => {
            
            const utid = transactionId();
                const initAsset = {
                    id:utid,
                    img:file.preview,
                    fid:null,
                    name:file.name,
                    desc:'',
                    type:'',
                    tags:[],
                    isLoading:true,
                    isNameEditMode:false,
                    isDescEditMode:false,
                    errorMsg:'',
                    hasErrors:false
                }
                this.props.setAsset(initAsset)

                const fd = new FormData()

                fd.append('file', file)

            return () => {

                return axios.post(CCM_DISPATCHER_FILENAME + "/api/v1/assets/upload", fd, { headers :{ 'Content-Type': 'multipart/form-data', 'X-Requested-With': 'XMLHttpRequest' } })
                    .then( response => {
                        const data = response.data
                        const payload = {
                            id:utid,
                            fid: data.id,
                            name:data.filename,
                            img: data.url,
                            type:"photo",
                            isLoading:false,
                            hasErrors:false,

                        }
                        this.props.updateAssetLoading(payload)
                        this.props.updateAssetImg(payload)
                    }).catch(error => {
                        const payload = {
                            id:utid,
                            isLoading:false,
                            errorMsg: error.response.data.errors[0],
                            hasErrors:true
                        }
                        this.props.updateAssetLoading({isLoading:false})
                        this.props.updateAssetImg(payload)

                        setTimeout(()=> {
                            this.props.removeAsset(utid)
                        }, 3500)
                    })
            }
        })

        this.chunkUploads(uploaders)
    }

    chunkUploads (uploads) {
        const first = uploads.shift()
        this.uploadChunk(first, uploads)
    }

    uploadChunk(chunk, chunks) {
        if (!chunk) return
        chunk().then(
            () => {
                const next = chunks.shift()
                this.uploadChunk(next, chunks)
            },
            () => console.log(arguments, 'fail')
        )
    }

    render () {

        const errorMsg = (
            <div className="alert alert-danger"><i className="fa fa-exclamation-triangle"></i> {this.props.bulkUpload.errorFilesMsg}</div>
        )

        return (
            <div>
                <section>
                    <FileTable />
                </section>
                <section>
                    <label>Add Files (Max {this.props.bulkUpload.maxAssets}.)</label>
                    <Dropzone onDrop={this.handleOnDrop.bind(this)} className="ignore">
                        <div className={this.props.bulkUpload.errorFiles ? "bulk-file-uploader error-warning" : "bulk-file-uploader"}>
                            <span className="file-upload-icon-container">
                                <span className="file-upload-icon"></span>
                                <span className="file-upload-btn">
                                    <a>Upload</a> or drag and drop files here
                                </span>
                            </span>
                            {this.props.bulkUpload.errorFiles ? errorMsg : null}
                        </div>
                    </Dropzone>
                </section>
            </div>
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
        setAsset: (payload) => dispatch(runAction("SET_ASSET_BULK", payload)),
        updateAssetLoading: (payload) => dispatch(runAction("UPDATE_ASSET_BULK_IS_LOADING", payload)),
        updateAssetImg: (payload) => dispatch(runAction("UPDATE_ASSET_BULK_IMG", payload)),
        toggleFilesErr: (payload) => dispatch(runAction("SET_FILES_ERROR", payload)),
        setFilesErrMsg: (payload) => dispatch(runAction("SET_FILES_ERROR_MSG", payload)),
        removeAsset: (payload) => dispatch(runAction("REMOVE_ASSET_BULK", payload)),
    }
}


export default connect(mapStateToProps, mapDispatchToProps)(BulkFiles);