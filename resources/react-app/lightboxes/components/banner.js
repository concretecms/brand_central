import React, { Component } from 'react'

class Banner extends Component {

    render() {
        if(!this.props.show) {
            return null;
        }

        return (
            <div className={this.props.err ? 'lightbox-banner-alert' : 'lightbox-banner-success'  }>
                <p>{this.props.msg}</p>
            </div>
        )
    }

}

export default Banner