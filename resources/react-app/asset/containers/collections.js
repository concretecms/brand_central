import React, { Component } from 'react'
import {connect} from 'react-redux'
import {runAction} from '../actions/assetActions'



class AssetCollections extends Component {
    
    render (){
        const handleClick = (item) => {
            this.props.removeItem(item)
        }

        const mapCollections = this.props.asset[this.props.field].map(collection => 
            <span className="round-tag-span" key={collection.id}>
                <span>{collection.name}</span>
                <span className="remove" onClick={()=> handleClick(collection.id) }><i className="fa fa-close"></i></span>
            </span>)

        return (
            <div>
                {mapCollections}
            </div>
        )
    }
}

const mapStateToProps = (state) => {
    return {
        asset:state.asset
    }
}

const mapDispatchToProps = (dispatch, props) => {
    return {
        removeItem : (payload) => dispatch(runAction(props.action, payload))
    }
}
export default connect(mapStateToProps, mapDispatchToProps )(AssetCollections)