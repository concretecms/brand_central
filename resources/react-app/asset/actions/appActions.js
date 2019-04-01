export const setIsLoading = (bool) => {
    return {
        type: "SET_IS_LOADING",
        payload: bool
    }
}

export const setIsSelectLoading = (bool) => {
    return {
        type: "SET_IS_SELECT_LOADING",
        payload: bool
    }
}