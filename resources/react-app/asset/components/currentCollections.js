import React, { Component } from 'react'

class CurrentCollections extends Component {

    render () {
        const handleClick = (item) => {
            this.props.removeItem(item)
        }

        const mapCollections = this.props.collections.map(collection =>
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

export default CurrentCollections
