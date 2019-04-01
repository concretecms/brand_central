export const setCollectionName = (name) => {
    return {
        type: "SET_COLLECTION_NAME",
        payload: name
    }
}

export const setCollectionDesc = (desc) => {
    return {
        type: "SET_COLLECTION_DESC",
        payload: desc
    }
}