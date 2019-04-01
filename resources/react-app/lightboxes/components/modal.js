import React, {Component} from "react"


class Modal extends Component {
    
    render () {
        if(!this.props.show) {
            return null;
        }

        const footer = (
            <div className="modal-footer">
                <button className="close-modal-btn" onClick={this.props.onClose}>Close</button>
                <button className="save-modal-btn" onClick={this.props.onSave}>Update File</button>
            </div>
        )

        return (
            <div className="react-modal">
                <div className="react-modal-content" style={{minWidth:this.props.width}}>
                    <div className="modal-header text-center">
                        <h3>{this.props.title}</h3>
                        <span className="close-modal" onClick={this.props.onClose}></span>
                    </div>
                    <div className="modal-body" style={{minHeight:this.props.height}}>
                        {this.props.children}
                    </div>
                    {this.props.controls ? footer : null}
                    
                </div>
            </div>
        )
    }

}

export default Modal