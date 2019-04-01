import React, { Component } from 'react'

class List extends Component {
    render () {
        const loading = (
            <div className="loader"><span></span></div>
        )
        const list = this.props.items.map(item => 
            <li key={item.id}>
                <span onClick={()=>this.props.itemClicked(item.id)}><i className="fa fa-th"></i> {item.name}</span>
            </li>       
        )
        return (
            <div>
                {this.props.isLoading ? loading : <div className="lightbox-list-container"><ul className="lightbox-list">{list}</ul></div>} 
                <div>
                    <hr/>
                    <span className="label-create-new">Create New Lightbox</span>
                </div>
            </div>
        )
    }
}

export default List