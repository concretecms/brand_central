import React from 'react'

export const PreviewThumbnail = (props) => {
    return (
        <div className="thumbnail-container preview-thumb">
            <img src={props.thumbnail} />
        </div>
    )
}