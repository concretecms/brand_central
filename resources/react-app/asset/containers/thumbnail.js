import React, {Component} from "react"
import {connect} from 'react-redux'
import {PreviewThumbnail} from '../components/thumbnail'
import {setAssetThumb, runAction} from '../actions/assetActions'
import Dropzone from 'react-dropzone'
import axios from "axios";
import { transactionId } from "../lib/assetServices";

class Thumbnail extends Component{

    handleOnDrop (files) {
        const uploaders = files.map( file => {
            
            const utid = transactionId();
          
            this.props.updateThumbId(utid)
            this.props.updateThumbLoad(true)
            const fd = new FormData()
            fd.append('file', file)
            return axios.post(CCM_DISPATCHER_FILENAME + "/api/v1/assets/upload", fd, { headers :{ 'Content-Type': 'multipart/form-data' } }).then( response => {
                const data = response.data
                this.props.updateThumbLoad(false)
                this.props.setAssetThumb(data.url)
                this.props.setAssetThumbId(data.id)                
            })
        })
       
    }

    render () {
        const handleClick = () => {
            this.props.clearThumbnail('')
        }
        
        const uploadThumb = (
            <div className="thumbnail-container empty-thumb">
                <span><a className="upload-thumb">Upload</a> or drag a thumbnail image here</span> 
            </div>
        )

        const loadingThumb = (
            <div className="loading-thumb"></div>
        )

        const emptyImg = (
            <Dropzone className="thumbnail-dropzone" onDrop={this.handleOnDrop.bind(this)} accept='image/*' multiple={false} >
                { this.props.app.thumbnail.isLoading ? loadingThumb : uploadThumb}
            </Dropzone>
        )

        return (
            <section>
                <div className="row">
                    <div className="col-xs-6">
                        <label>Thumbnail Image</label> 
                    </div>
                    <div className="col-xs-6 text-right">
                        <span>{this.props.asset.thumbnail ? 
                            <span onClick={handleClick} className="clear-thumb"><i className="fa fa-trash"></i> Remove Thumbnail</span>  : '' }
                        </span>
                    </div>
                </div>
                
                { this.props.asset.thumbnail ? 
                    <PreviewThumbnail thumbnail={this.props.asset.thumbnail} /> : 
                    emptyImg }
            </section>
        )
    }    
}


const mapStateToProps = (state) => {
    return {
        asset:state.asset,
        app:state.app
    }
}

const mapDispatchToProps = (dispatch) => {
    return {
        clearThumbnail: (payload) => dispatch(setAssetThumb(payload)),
        updateThumbId: (payload) => dispatch(runAction("UPDATE_THUMBNAIL_ID", payload)),
        updateThumbLoad: (payload)=> dispatch(runAction("UPDATE_THUMBNAIL_LOADING", payload)),
        setAssetThumb: (payload) => dispatch(runAction("SET_ASSET_THUMB", payload)),
        setAssetThumbId: (payload) => dispatch(runAction("SET_ASSET_THUMB_ID", payload))
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(Thumbnail);