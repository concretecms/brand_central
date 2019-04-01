import React, {Component} from "react"
import {connect} from 'react-redux'
import Dropzone from 'react-dropzone'
import {transactionId} from "../lib/assetServices";
import axios from "axios";
import {runAction} from "../actions/assetActions";

import Modal from "../components/modal";
import Input from '../components/input'

class Files extends Component {
    handleOnDrop(files) {
        const uploaders = files.map(file => {
            const utid = transactionId();
            const initFile = {
                desc: file.desc,
                filename: file.name,
                id: utid,
                isLoading: true,
                errorMsg: '',
                hasErrors: false,
            }
            this.props.setFile(initFile)

            const fd = new FormData()
            fd.append('file', file)
            return axios.post("/api/v1/assets/upload", fd, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => {
                    const data = response.data
                    const payload = {
                        id: utid,
                        fid: data.id,
                        img: data.url,
                        desc: data.desc,
                        filename: data.filename,
                        isLoading: false,
                        errorMsg: '',
                        hasErrors: false
                    }
                    this.props.updateFile(payload)
                    this.props.updateFileErr(false)
                })
                .catch(error => {
                    const payload = {
                        id: utid,
                        isLoading: false,
                        errorMsg: error.response.data.errors[0],
                        hasErrors: true
                    }
                    this.props.updateFile(payload)

                    setTimeout(() => {
                        this.props.removeFile(utid)
                    }, 3500)
                })
        })
    }

    handleFileDeletion(file) {
        this.props.removeFile(file)
    }


    openFile(id) {
        this.props.modalVisibility(true)
        const content = this.props.asset.files.filter(file => file.id === id)
        const payload = {
            id: content[0].id,
            fid: content[0].fid,
            desc: content[0].desc,
            img: content[0].img,
            filename: content[0].filename
        }
        this.props.updateSelectedFile(payload)
    }

    closeFile() {
        this.props.modalVisibility(false)
    }

    saveFile() {
        this.props.modalVisibility(false)
        const payload = {
            id: this.props.app.currentFile.id,
            desc: this.props.app.currentFile.desc,
            fid: this.props.app.currentFile.fid,
            img: this.props.app.currentFile.img,
            filename: this.props.app.currentFile.filename,
            isLoading: false
        }
        this.props.updateFile(payload)
    }

    render() {

        const mappedFiles = this.props.files.map(file =>
            <li key={file.id}>
                <div>
                    <span className={file.isLoading ? 'file-disabled' : 'file-item'}
                          onClick={() => this.openFile(file.id)}>
                        {file.hasErrors ? <span className="label label-danger">{file.errorMsg}</span> : file.desc}
                    </span>
                    {file.isLoading ? <span className="file-processing-icon file-icon-pull-right"></span> :
                        <span className="file-delete-icon file-icon-pull-right" onClick={() => this.handleFileDeletion(file.id)}></span>}
                </div>
            </li>)


        const selectedFile = (
            <div>
                <img src={this.props.app.currentFile.img} style={{padding: '5px', width: '100%'}}/>
                <div className="text-center"><span
                    className="label label-default">{this.props.app.currentFile.filename}</span></div>
                <section>
                    <label>Description </label>
                    <input value={this.props.app.currentFile.desc} onChange={(e) => {
                        this.props.updateSelectedFileDesc(e.target.value)
                    }}/>
                </section>
            </div>
        )


        return (
            <section>
                <label>Files <span>*</span></label>
                <div
                    className={this.props.app.errorFiles ? 'files-container error-warning-border-only' : 'files-container'}>
                    <Dropzone onDrop={this.handleOnDrop.bind(this)} className="ignore" disableClick={true}>
                        <div className="stored-files">
                            <ul>
                                {mappedFiles}
                            </ul>
                        </div>
                    </Dropzone>
                    <Dropzone onDrop={this.handleOnDrop.bind(this)} className="ignore">
                        <div className="files-uploader text-center">
                        <span className="clearfix">
                            {this.props.app.errorFiles ? <span className="alert alert-danger alert-files">You need to upload at least one file.</span> : null}
                            <span className="file-upload-icon"></span>
                            <span className="file-upload-btn">
                                <a>Upload</a> or drag and drop files here
                            </span>
                        </span>
                        </div>
                    </Dropzone>
                </div>

                <Modal title="File Details"
                       show={this.props.app.modal.isVisible}
                       onClose={() => this.closeFile()}
                       onSave={() => this.saveFile()}
                       controls="false" width="700px" height="400px">
                    <div>
                        {selectedFile}
                    </div>
                </Modal>
            </section>
        )
    }
}

const mapStateToProps = (state) => {
    return {
        asset: state.asset,
        app: state.app
    }
}

const mapDispatchToProps = (dispatch) => {
    return {
        setFile: (payload) => dispatch(runAction("SET_ASSET_FILES", payload)),
        updateFile: (payload) => dispatch(runAction("UPDATE_ASSET_FILE", payload)),
        removeFile: (payload) => dispatch(runAction("REMOVE_ASSET_FILE", payload)),
        modalVisibility: (payload) => dispatch(runAction("SET_MODAL_VISIBILITY", payload)),
        updateSelectedFile: (payload) => dispatch(runAction("SET_CURRENT_SELECTED_FILE", payload)),
        updateSelectedFileDesc: (payload) => dispatch(runAction("UPDATE_CURRENT_SELECTED_FILE_DESC", payload)),
        updateFileErr: (payload) => dispatch(runAction("SET_ERROR_FILES", payload))
    }
}


export default connect(mapStateToProps, mapDispatchToProps)(Files);