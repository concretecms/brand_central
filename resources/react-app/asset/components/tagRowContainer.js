import React, {Component} from 'react'

class TagRowContainer extends Component {

    render () {
        const tags = this.props.tags.map(tag => 
            <span className="label label-default" key={tag.id}>
                {tag.name}  
                <span onClick={()=>this.props.removeTag(tag.id,this.props.asset)} className="remove-tag-btn"> <i className="fa fa-times"></i> </span>
            </span>)

        return (
            <section>
                <span className="add-tag-btn" onClick={()=>this.props.openModal(this.props.asset, this.props.file)}>Add Tag <i className="fa fa-plus"></i></span>
                <span className="generate-tag-btn" onClick={()=>this.props.getTags(this.props.asset, this.props.file)}>
                    Generate Tags {this.props.isLoading ? <i className="fa fa-spinner fa-spin"></i> : <i className="fa fa-tag"></i> }
                </span>
                {tags}
            </section>
        )
    }
}
export default TagRowContainer